<?php


namespace App\Serializer;



use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserContextBuilder implements SerializerContextBuilderInterface
{

    /**
     * @var SerializerContextBuilderInterface
     */
    private $contextBuilder;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $checker;

    public function __construct(SerializerContextBuilderInterface $contextBuilder, AuthorizationCheckerInterface $checker)
    {
        $this->contextBuilder = $contextBuilder;
        $this->checker = $checker;
    }

    /**
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     * @return array
     * * this will add a groups to the entity (admin_readable) fetch if the user match a certain rule ( ROLE_ADMIN)
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->contextBuilder->createFromRequest($request, $normalization, $extractedAttributes);
        // * class being serialized
        $resourceClass = $context['resource_class'] ?? null; // * default value to null if not set

        // * if the currently authenticated user has the role admin
        if (User::class === $resourceClass
            && isset($context['groups'])
            && $normalization === true
            && $this->checker->isGranted(User::ROLE_ADMIN)) {
            $context['groups'][] = 'admin_readable';
        }

        return $context;
    }
}
