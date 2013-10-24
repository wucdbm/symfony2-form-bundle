<?php

namespace Sirian\FormBundle\Form\Type;

use Sirian\FormBundle\Form\DataTransformer\SuggestTransformer;
use Sirian\FormBundle\Suggest\Registry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SuggestType extends AbstractType
{
    private $registry;

    public function __construct(Registry $registry)
    {

        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new SuggestTransformer($this->registry->getSuggester($options['suggester']), $options['multiple']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'multiple' => false,
            'compound' => false,
            'select2_options' => []
        ]);

        $resolver->setRequired(['suggester']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'multiple' => $options['multiple'],
            'choices' => $options['multiple'] ? $view->vars['value'] : [$view->vars['value']],
            'suggester' => $options['suggester']
        ]);

        $view->vars = array_replace($view->vars, [
            'ids' => array_map(function ($choice) {
                return $choice['id'];
            }, $view->vars['choices']),
            'texts' => array_map(function ($choice) {
                return $choice['text'];
            }, $view->vars['choices']),
        ]);
    }


    public function getName()
    {
        return 'suggest';
    }
}
