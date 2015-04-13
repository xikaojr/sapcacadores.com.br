<?php

class App_Form_Abstract extends Zend_Form {

    public function init() {
        parent::init();
        $this->setDisableLoadDefaultDecorators(true);
    }

    public function removeAllDecorators($elemento) {
        $elemento->removeDecorator('HtmlTag')
                ->removeDecorator('Label')
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper');
        return $this;
    }

    /**
     * Set required flag
     *
     * @param  bool $flag Default value is true
     * @return Zend_Form_Element
     */
    public function setRequired($flag = true) {
        $this->_required = (bool) $flag;

        if ($this->_required) {
            $this->setAttribs(array('class' => 'required ' . $this->getAttrib('class')));
        }

        return $this;
    }

    public function addElement($element, $name = null, $options = null) {


        if ($element instanceof Zend_Form_Element && $element->isRequired()) {
            $element->setAttrib('class', 'required ' . str_replace('required', '', $element->getAttrib('class')));
        } elseif ($options && $options['required']) {
            $options['attribs']['class'] = 'required ';
        }

//        if ($element->isRequired() && !$element instanceof Zend_Form_Element_Checkbox && !$element instanceof Zend_Form_Element_Radio) {
//            $element->setAttribs(array('autofocus' => '', 'required' => ''));
//        }

        if (!$element instanceof Zend_Form_Element_Checkbox && !$element instanceof Zend_Form_Element_MultiCheckbox && !$element instanceof Zend_Form_Element_Radio && !$element instanceof Zend_Form_Element_File) {
            $element->setAttrib('class', 'form-control ' . $element->getAttrib('class'));
        }

        $element->addDecorator('Label');
        $element->getDecorator('Label')->setTag(null);
        $element->addDecorator('FormElements');
        $element->removeDecorator('HtmlTag');
        $element->removeDecorator('DtDdWrapper');

        return parent::addElement($element, $name, $options);
    }

}
