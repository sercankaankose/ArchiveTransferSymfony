<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Authors;
use App\Entity\Citations;
use App\Entity\Issues;
use App\Entity\Journal;
use App\Entity\Translations;
use App\Form\ArticleFormType;
use App\Form\IssuesFormType;
use App\Form\JournalFormType;
use App\Params\ROLE_PARAM;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadCrumbService;


class HomepageController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BreadCrumbService $breadcrumbService;

    public function __construct(EntityManagerInterface $entityManager, BreadCrumbService $breadcrumbService)
    {
        $this->entityManager = $entityManager;
        $this->breadcrumbService = $breadcrumbService;
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $breadcrumb = $this->breadcrumbService->createEmptyBreadcrumb();

        if ($this->getUser() === null) {
            $this->redirectToRoute('app_login');
        }
        return $this->render('homepage/index.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/journal/{id}/issues', name: 'journal_issues')]
    public function journal_issues($id, FactoryInterface $factory,): Response
    {
        $journal = $this->entityManager->getRepository(Journal::class)->find($id);

        $breadcrumb = $this->breadcrumbService->createJournalIssueBreadcrumb($factory, $journal->getName());

        if (!$journal) {
            $this->addFlash('danger', 'Dergi Bulunamadı.');
            if (in_array($this->getUser()->getRoles(), (array)ROLE_PARAM::ROLE_ADMIN)) {
                return $this->redirectToRoute('admin_journal_management');
            } else {
                return $this->redirectToRoute('app_homepage');
            }
        }
        $issues = $this->entityManager->getRepository(Issues::class)->findBy([
            'journal' => $journal
        ]);
        return $this->render('journal-number.html.twig', [
            'breadcrumb' => $breadcrumb,
            'journal' => $journal,
            'issues' => $issues,

        ]);
    }


    #[Route('/journal/{id}/issue/add', name: 'journal_issue_add')]
    public function issue_add($id, Request $request, FactoryInterface $factory, MessageBusInterface $messageBus): Response
    {
        $journal = $this->entityManager->getRepository(Journal::class)->find($id);
        $journalname = $journal->getName();
        $breadcrumb = $this->breadcrumbService->createIssueAddBreadcrumb($factory, $journalname, $id);

        $newissue = new Issues();
        $form = $this->createForm(IssuesFormType::class, $newissue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newissue->setJournal($journal);
            $uniq = uniqid();
            $existingIssue = $this->entityManager->getRepository(Issues::class)->findOneBy([
                'journal' => $journal,
                'number' => $newissue->getNumber(),
                'year' => $newissue->getYear(),
            ]);

            if ($existingIssue) {
                $this->addFlash('danger', 'Bu yıl ve sayı zaten var.');
                return $this->redirectToRoute('journal_issue_add', ['id' => $id]);
            }

            $pdfFile = $form->get('fulltext')->getData();
            if ($pdfFile) {
                $pdfFileName = $this->generateHashedFileName($pdfFile, 'pdf');
                try {
                    $pdfFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/pdf', $pdfFileName);
                    $newissue->setFulltext('/uploads/' . $pdfFileName);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            } else {
                $newissue->setFulltext(null);
            }

            $xmlFile = $form->get('xml')->getData();
            if ($xmlFile) {
                $xmlFileName = $this->generateHashedFileName($xmlFile, 'xml');
                try {
                    $xmlFile->move(
                        $this->getParameter('kernel.project_dir') . '/var/xml', $xmlFileName);
                    $newissue->setXml('var/' . $xmlFileName);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            }
            $newissue->setStatus('waiting');
            $this->entityManager->persist($newissue);
            $this->entityManager->flush();
// bu kısımda parselanmak üzerine gönderim olacak

            $this->addFlash(
                'success',
                'Yeni Sayı Oluşturulmuştur.'
            );
            return $this->redirectToRoute('journal_issues', ['id' => $id]);
        }
        return $this->render('journal_issue_add.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => $breadcrumb,
            'journal' => $journal,
        ]);
    }

    #[Route('journal/issue/{id}/articles', name: 'articles_list')]
    public function articleList($id, FactoryInterface $factory): Response
    {
        $issue = $this->entityManager->getRepository(Issues::class)->find($id);
        $journal = $issue->getJournal();

        $breadcrumb = $this->breadcrumbService->createArticle_listBreadcrumb($factory, $journal->getName(), $issue->getNumber(), $journal->getId());

        if (!$journal && !$issue) {
            $this->addFlash('danger', 'Dergi ya da sayı Bulunamadı.');
            if (in_array($this->getUser()->getRoles(), (array)ROLE_PARAM::ROLE_ADMIN)) {
                return $this->redirectToRoute('journal_issues',['id'=>$journal->getId()]);
            } else {
                return $this->redirectToRoute('app_homepage');
            }
        }

        $articles = $this->entityManager->getRepository(Articles::class)->findBy([
            'issue' => $issue
        ]);

        return $this->render('articles_list.html.twig', [
            'breadcrumb' => $breadcrumb,
            'articles' => $articles,
            'issues' => $issue,
            'journal' => $journal,

        ]);
    }


    #[Route('article/edit/{id}', name: 'article_edit')]
    public function article_edit($id, Request $request, FactoryInterface $factory): Response
    {

        $article = $this->entityManager->getRepository(Articles::class)->find($id);
        $issue = $article->getIssue();
        $journal = $article->getJournal();
        $breadcrumb = $this->breadcrumbService->createArticleEditBreadcrumb($factory, $journal->getName(), $issue->getNumber(), $issue->getId(), $journal->getId());
        $pdfFileName = trim($article->getFulltext(),'var/uploads/articlepdf/');
        $pdfFileName = $pdfFileName . 'pdf';
        $translations = $this->entityManager->getRepository(Translations::class)->findBy(['article'=>$article]);
        $citations = $this->entityManager->getRepository(Citations::class)->findBy(['article'=>$article]);
        $authors = $this->entityManager->getRepository(Authors::class)->findBy(['article'=>$article]);
        if (!$article && !$issue && !$article) {
            $this->addFlash('danger', 'Dergi, sayı veya makale hatalı.');
            return $this->redirectToRoute('admin_journal_management');
        }
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Makale bilgileri güncellendi.');

            return $this->redirectToRoute('articles_list', ['id' => $issue->getId()]);
        }


        return $this->render('article_edit.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => $breadcrumb,
           'pdfFile' =>$pdfFileName,
            'article'=> $article

        ]);
    }
    #[Route('/article/pdf/{filename}', name: 'article_pdf')]
    public function showPdfAction($filename)
    {
        $pdfPath = $this->getParameter('pdf_directory').'/'.$filename;

        // Check if the file exists
        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException('The file does not exist');
        }

        // Send the file as a response
        $response = new Response(file_get_contents($pdfPath));
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    private function generateHashedFileName(UploadedFile $file, string $subfolder): string
    {
        $hash = hash_file('sha256', $file->getPathname());
        $extension = $file->guessExtension();

        return $subfolder . '/' . $hash . '.' . $extension;
    }

}
