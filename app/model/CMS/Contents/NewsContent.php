<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michal
 * Date: 15.11.12
 * Time: 13:27
 * To change this template use File | Settings | File Templates.
 */
namespace SRS\Model\CMS;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 *
 * @property integer $count
 */
class NewsContent extends \SRS\Model\CMS\Content implements IContent
{
    protected $contentType = 'newscontent';
    protected $contentName = 'Aktuality';

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $count;

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function getCount()
    {
        return $this->count;
    }





    public function addFormItems(\Nette\Application\UI\Form $form) {
        parent::addFormItems($form);
        $formContainer = $form[$this->getFormIdentificator()];
        $formContainer->addText("count",'Počet zobrazovaných aktualit:')
            ->addRule(\Nette\Application\UI\Form::INTEGER, 'Musí být číslo')
            ->setDefaultValue($this->count)
            ->getControlPrototype()->class('number');
        return $form;
    }

    public function setValuesFromPageForm(\Nette\Application\UI\Form $form) {
        parent::setValuesFromPageForm($form);
        $values = $form->getValues();
        $values = $values[$this->getFormIdentificator()];
        $this->count = $values['count'];
    }

    public function getContentName() {
        return $this->contentName;
    }





}