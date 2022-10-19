<?php

namespace App\Controller;

use App\Entity\Crud;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class CrudAPIController extends AbstractController
{
   /**
     * @param ManagerRegistry $doctrine
     */

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em =  $doctrine->getManager();
    }


    // Index method
    #[Route('api/index', name: 'app_index', methods:['GET'])]
    public function index(): JsonResponse
    {
        $rawData =  $this->em->getRepository(Crud::class)->findAll();

        $data=[];
        foreach($rawData as $rd){
            $data[] = [
                'id' => $rd->getId(),
                'title'=>$rd->getTitle(),
                'content'=> $rd->getContent()
            ];
        }
        return $this->json([
            'status'=>'Ok',
            'data' => $data,
        ]);
    }

    // Create method
    #[Route('api/create', name:'app_create', methods:['POST'])]
    public function create(Request $request): JsonResponse{
        $requestedData =  json_decode($request->getContent(), true);

        $table =  new Crud();
        $table->setTitle($requestedData["title"]);
        $table->setContent($requestedData["content"]);
        
        $this->em->persist($table);
        $this->em->flush();

        return $this->json([
            'status'=> 'Ok',
            'message'=> 'Data successfully  inserted',
            'data'=> $requestedData,

        ]);
    }

    // Show method
    #[Route('api/show/{id}', name:'app_show', methods:['GET'])]
    public function show($id): JsonResponse{

        $rawData= $this->em->getRepository(Crud::class)->find($id);

        if(!$rawData){
            return $this->json([
                "status"=>"Ok",
                "message"=>'No data found with this Id',
                'id'=>$id
            ], 404);
        }
        $data =  [
            'id' =>$rawData->getId(),
            'title' =>$rawData->getTitle(),
            'content' => $rawData->getContent()
        ];

        return $this->json([
            'status'=>'Ok',
            'data'=> $data
               ]);
    }

    // Update method
    #[Route('api/update/{id}',name:'app_update', methods:['PUT'])]
    public function update(Request $request, $id): JsonResponse{

        $rawData= $this->em->getRepository(Crud::class)->find($id);
        if(!$rawData){
            return $this->json([
                "status"=>"Ok",
                "message"=>'No data found with this Id',
                'id'=>$id
            ], 404);
        }

        $rawData->setTitle($request->get('title'));
        $rawData->setContent($request->get('content'));

        $this->em->flush();

        $data = [
            'id'=>$rawData->getId(),
            'title'=>$rawData->getTitle(),
            'content'=>$rawData->getContent()
        ];

        $this->json([
            'status'=>'Ok',
            'message'=> 'Data successfully  updated',
            'data'=> $data
        ]);
    }

    // Delete method
    #[Route('api/delete/{id}', name:'app_delete', methods:['DELETE'])]
    public function delete($id): JsonResponse{
        $data = $this->em->getRepository(Crud::class)->find($id);

        if(!$data){
            return $this->json([
                "status"=>"Ok",
                "message"=>'No data found with this Id',
                'id'=>$id
            ], 404);
        }

        $this->em->remove($data);
        $this->em->flush();

        return $this->json([
            'status'=> 'Ok',
            'message'=> 'Data successfully  deleted',
        ]);
    }
}
