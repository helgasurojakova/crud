<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/tasks')]
class TaskController extends AbstractController
{
  #[Route(path: '', methods: ['GET'])]
  public function list(TaskRepository $taskRepository): Response
  {
    $tasks = $taskRepository->findAll();

    $data = [];
    foreach ($tasks as $task) {
      $data[] = [
        'id' => $task->getId(),
        'title' => $task->getTitle(),
        'description' => $task->getDescription(),
      ];
    }

    return new JsonResponse($data);
  }

  #[Route(path: '/{id}', methods: ['GET'])]
  public function get(TaskRepository $taskRepository, $id): Response
  {
    $task = $taskRepository->find($id);

    if ($task === null) {
      $response = new JsonResponse("Запись не найдена.", 200, [], true);
      $response->setContent(json_encode("Запись не найдена.", JSON_UNESCAPED_UNICODE));
      return $response;
    }

    $data = [
      'id' => $task->getId(),
      'title' => $task->getTitle(),
      'description' => $task->getDescription(),
    ];

    return new JsonResponse($data);
  }

  #[Route(path: '/create', methods: ['POST'])]
  public function create(Request $request, EntityManagerInterface $entityManager): Response
  {
    $requestData = json_decode($request->getContent(), true);

    $task = new Task();
    $task->setTitle($requestData['title']);
    $task->setDescription($requestData['description']);

    $entityManager->persist($task);
    $entityManager->flush();

    $data = [
      'id' => $task->getId(),
      'title' => $task->getTitle(),
      'description' => $task->getDescription(),
    ];

    return new JsonResponse($data);
  }

  #[Route(path: '/{id}/update', methods: ['PUT'])]
  public function update(Request $request, TaskRepository $taskRepository, $id, EntityManagerInterface $entityManager): Response
  {
    $requestData = json_decode($request->getContent(), true);
    $task = $taskRepository->find($id);

    $task->setTitle($requestData['title']);
    $task->setDescription($requestData['description']);

    $entityManager->flush();

    $data = [
      'id' => $task->getId(),
      'title' => $task->getTitle(),
      'description' => $task->getDescription(),
    ];

    return new JsonResponse($data);
  }

  #[Route(path: '/{id}', methods: ['DELETE'])]
  public function delete(TaskRepository $taskRepository, $id, EntityManagerInterface $entityManager): Response
  {
    $task = $taskRepository->find($id);

    $entityManager->remove($task);
    $entityManager->flush();

    return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
  }
}
