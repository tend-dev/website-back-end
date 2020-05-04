<?php

declare(strict_types=1);

namespace App\Action\Api;

use App\Entity\Post;
use App\Repository\PostRepository;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\StorageInterface;

final class PostListAction
{

    /** @var StorageInterface */
    private $storage;

    /** @var PostRepository */
    private $repository;

    public function __construct(PostRepository $repository, StorageInterface $storage) {
        $this->repository = $repository;
        $this->storage = $storage;
    }

    /**
     * @Route(
     *     "/posts",
     *     name="post_list",
     *     methods={"GET"}
     * )
     * @SWG\Get(
     *     path="/posts",
     *     tags={"Post"},
     *     summary="Retrieves the collection of Posts resources.",
     *     @SWG\Response(
     *         response=200,
     *         description="Post collection response"
     *     )
     * )
     */
    public function __invoke(): Response
    {
        $items = [];
        /** @var Post $Post */
        foreach ($this->repository->findByLimit() as $post) {
            $items[] = [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'created' => $post->getCreatedAt()->format('Y-m-d'),
                'image' => $this->storage->resolveUri($post, 'imageFile', Post::class),
            ];
        }

        return new JsonResponse($items);
    }
}
