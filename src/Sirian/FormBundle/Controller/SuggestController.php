<?php

namespace Sirian\FormBundle\Controller;

use Sirian\FormBundle\Suggest\Registry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestController extends Controller
{
    public function suggestAction($name)
    {
        /**
         * @var Registry $registry
         */
        $registry = $this->get('sirian_form.suggest_registry');
        $request = $this->getRequest();

        if (!$registry->hasSuggester($name)) {
            throw $this->createNotFoundException();
        }

        $suggester = $registry->getSuggester($name);

        $query = $request->query->get('query', '');
        $options = $request->query->get('o', []);

        $data = $suggester->suggest($query, $options);
        $data->setItems($suggester->transform($data->getItems()));
        return new JsonResponse($data->toArray());
    }
}
