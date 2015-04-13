<?php   
class Zend_View_Helper_TranslateMessage extends Zend_View_Helper_Abstract {

    private $_messages = null;

    public function TranslateMessage($originMessage)
    {
        
        //$this->_setMessages();
        if(count($this->_messages))
        {
            foreach($this->_messages as $constraint_name => $message)
            {
                if ( strpos($originMessage , $constraint_name) )
                {
                    return $message;
                }
            }
        }
        return $originMessage ;
    }
    
    /**
     * Sets the $_messages hash
     * @return void.
     */
    protected function _setMessages()
    {
        $obj = new ConstraintMsg();
        $rows = $obj->fetchAll()->toArray();
        
        if(count($rows))
        {
            foreach($rows as $r)
            {
                $this->_messages[$r["constraint_name"]] = $r["mensagem"];
            }
        }
    }



}

