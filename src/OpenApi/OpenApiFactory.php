<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements  OpenApiFactoryInterface
{


    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        foreach ($openApi->getPaths()->getPaths() as $key => $path){
            if($path->getGet() && $path->geGet()->getSummary ==='hidden'){

                $openApi->getPaths()->addPath($key,$path->withGet(null));

            }
        }
        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['bearerAuth'] = new \ArrayObject([
            'type'=>'http',
            'scheme'=>'bearer',
            'bearerFormat'=>'JWT'
        ]);
        $schemas=$openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type'=>'object',
            'properties'=>[
                'username'=>[
                    'type'=>'string',
                    'example'=>'john@doe.fr'
                ],
                'password'=>[
                    'type'=>'string',
                    'example'=>'00ercc'
                ],
            ]
        ]);
        $userconnectedOperation = $openApi->getPaths()->getPath('/me')->getGet()->withParameters([]);
        $connectedPathItem = $openApi->getPaths()->getPath('/me')->withGet($userconnectedOperation);
        $openApi->getPaths()->addPath('/me',$connectedPathItem);
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'userApiLogin',
                tags: ['User'],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                            'application/json'=>[
                                'schema' =>[
                                    '$ref'=>'#/components/schemas/Credentials'
                                ]
                            ]

                        ]

                    )
                ),
                responses:[
                    '200' =>[
                        'description' =>'User connected',
                        'content'=>[
                            'application/json'=>[
                                'schema'=>[
                                    '$ref'=>'#/components/schema/User-read.User'
                                ]
                            ]
                        ]
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/login',$pathItem);
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'userApiLogout',
                tags: ['User'],
                responses:[
                    '204' =>[]
                ]
            )
        );
        $openApi->getPaths()->addPath('/logout',$pathItem);
        return $openApi;
    }
}