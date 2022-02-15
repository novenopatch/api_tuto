<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Post;
use App\Security\Voter\PostVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PostApiNormalizer implements ContextAwareNormalizerInterface,NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const ALREADY_CALLED_NORMALIZER = 'UserOwnedNormalizerCalled';
    public function __construct(
        //private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        $alreadyCalled= $context[self::ALREADY_CALLED_NORMALIZER] ?? false;
       return $data instanceof Post && $alreadyCalled==false;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_NORMALIZER] = true;
        if(
            $this->authorizationChecker->isGranted(PostVoter::CAN_EDIT,$object)
            &&
            isset($context['groups'])
        ) {
        $context['groups'][]='read:collection:User';
        }
        return $this->normalizer->normalize($object,$format,$context);
    }


}