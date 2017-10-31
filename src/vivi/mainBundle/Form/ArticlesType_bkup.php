<?php

namespace vivi\mainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use vivi\mainBundle\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use vivi\mainBundle\Entity\Articles;
use vivi\mainBundle\Entity\Category;
use vivi\mainBundle\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class ArticlesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('title', TextType::class, array('label'=>'Title'))
            ->add('content', TextareaType::class, ['attr'=>['rows'=> 4], 'label'=>'Content'])
            ->add('author', TextType::class,array('label'=>'Author'))
            ->add('image', FileType::class,['required' => false, 'mapped'=>false])
            ->add('categories', EntityType::class,[
                'class' => 'vivimainBundle:Category',
                'choice_label' => 'name',
                'required' => false,
                'mapped'=>false,
                ])
            
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'vivi\mainBundle\Entity\Articles',
            
        ));
    }
}
