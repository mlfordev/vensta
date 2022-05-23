<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdController extends AbstractController
{
    protected AdRepository $adRepository;

    public function __construct(AdRepository $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[Route('/{page<\d+>?1}', name: 'ad_index', methods: ['GET'])]
    public function index(int $page): Response
    {
        if ($page < 1) {
            throw $this->createNotFoundException();
        }
        /** @var UrlGeneratorInterface $router */
        $router = $this->container->get('router');
        $pagination = $this->adRepository->getPagination($router, $page);
        $data = $pagination->getData();
        if (empty($data) && $page !== 1) {
            throw $this->createNotFoundException();
        }
        return $this->render('ad/index.html.twig', [
            'ads' => $data,
            'pager' => $pagination->getPager(),
        ]);
    }

    #[Route('view/{pageId<\d+>?1}', name: 'ad_show', methods: ['GET'])]
    public function show(int $pageId): Response
    {
        $ad = $this->adRepository->find($pageId);
        if (!$ad) {
            throw $this->createNotFoundException();
        }
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route('create', name: 'ad_create', methods: ['GET', 'POST'])]
    public function create(Request $request, FileUploader $fileUploader): Response
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ad = $form->getData();
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $ad->setImage($imageFileName);
            }
            $this->adRepository->add($ad);
            return $this->redirectToRoute('ad_index');
        }
        return $this->renderForm('ad/create.html.twig', [
            'form' => $form,
        ]);
    }
}