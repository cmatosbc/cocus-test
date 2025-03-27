<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/notes')]
class NoteController extends AbstractController
{
    public function __construct(
        private NoteRepository $noteRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'note_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        $notes = $this->noteRepository->findAllNotesByUser($user);

        return $this->json($notes);
    }

    #[Route('/search', name: 'note_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $message = $request->query->get('message');
        $type = $request->query->get('type');

        if ($message && $type !== null) {
            $notes = $this->noteRepository->searchByMessageAndType($message, (int)$type);
        } elseif ($message) {
            $notes = $this->noteRepository->searchByMessage($message);
        } elseif ($type !== null) {
            $notes = $this->noteRepository->getAllByType((int)$type);
        } else {
            return $this->json(['error' => 'No search criteria provided'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($notes);
    }

    #[Route('/{id}', name: 'note_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $note = $this->noteRepository->findOneByUser($this->getUser(), $id);

        if (!$note) {
            return $this->json(['error' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($note);
    }

    #[Route('', name: 'note_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['message']) || !isset($data['type'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $note = new Note();
        $note->setMessage($data['message']);
        $note->setType($data['type']);
        $note->setUser($this->getUser());

        $errors = $this->validator->validate($note);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->noteRepository->save($note);

        return $this->json($note, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'note_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $note = $this->noteRepository->findOneByUser($this->getUser(), $id);

        if (!$note) {
            return $this->json(['error' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['message'])) {
            $note->setMessage($data['message']);
        }
        if (isset($data['type'])) {
            $note->setType($data['type']);
        }

        $errors = $this->validator->validate($note);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->noteRepository->save($note);

        return $this->json($note);
    }

    #[Route('/{id}', name: 'note_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $note = $this->noteRepository->findOneByUser($this->getUser(), $id);

        if (!$note) {
            return $this->json(['error' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }

        $this->noteRepository->delete($note);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
