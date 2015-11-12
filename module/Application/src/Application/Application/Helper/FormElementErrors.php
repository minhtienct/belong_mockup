<?php

namespace Application\Application\Helper;

use Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;


/**
 * フォームエレメントエラーのクラス
 */
class FormElementErrors extends OriginalFormElementErrors
{
    /**
     *
     * @var html tag 
     */
    protected $messageCloseString = '</span></div>';
    
    /**
     *
     * @var html tag 
     */
    protected $messageOpenFormat = '<div class="messages_form_error"><span>';
    
    /**
     *
     * @var html tag 
     */
    protected $messageSeparatorString = '</span><span>';
}
