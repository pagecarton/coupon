<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Coupon_Table_Abstract
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Tuesday 16th of November 2021 09:22PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class Coupon_Table_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'table_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'table_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Coupon_Table';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 99, 98 );


    /**
     * creates the form for creating and editing page
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )  
    {
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
		$form->submitValue = $submitValue ;

		$fieldset = new Ayoola_Form_Element;
    
        if( ! empty( $values['code'] ) )
        {
            $fieldset->addElement( array( 'name' => 'code', 'label' => 'Promo Code', 'placeholder' => 'e.g. SALE67', 'type' => 'InputText', 'value' => @$values['code'] ) );         
        }
        else
        {
            $fieldset->addElement( array( 'name' => 'code-x', 'label' => 'Promo Code', 'disabled' => true, 'placeholder' => 'e.g. SALE67', 'type' => 'InputText', 'value' => @$values['code'] ) );         
        }
        $fieldset->addElement( array( 'name' => 'value', 'label' => 'Value of Promo Code', 'placeholder' => 'e.g. 400', 'type' => 'InputText', 'value' => @$values['value'] ) );         

        $type = array(
            'constant' => 'Fixed (Constant) Amount',
            'percentage' => 'Percentage of Total Order',
        );

        $fieldset->addElement( array( 'name' => 'type', 'label' => 'Value Type', 'type' => 'Select', 'value' => @$values['type'] ), $type );  
        
        $v = array();
        if( ! empty( $values['product'] ) )
        {   
            foreach( $values['product'] as $each )
            {
                $record = Application_Article_Table::getInstance()->selectOne( null, array( 'article_url' => $each ) );
                $v[$each] = $record['article_title'];
            }
        }

        $fieldset->addElement( 
            array( 
            'name' => 'product', 
            'label' => 'Apply only to these products (Apply to all by default)', 
            'config' => array( 
                'ajax' => array( 
                    'url' => '' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_Search?article_type=product',
                    'delay' => 1000
                ),
                'placeholder' => 'e.g. Type Product Title',
                'minimumInputLength' => 2,   
            ), 
            'multiple' => 'multiple', 
            'type' => 'Select2', 
            'value' => $v 
            )
            ,
            $v
        ); 

        $postTypesAvailable = Application_Article_Type_TypeAbstract::getMyAllowedPostTypes();
        asort( $postTypesAvailable );

        $fieldset->addElement( array( 'name' => 'product_type', 'label' => 'Apply only to these post types (Apply to all by default)',  'type' => 'SelectMultiple', 'multiple' => true, 'value' => @$values['product_type'] ), $postTypesAvailable );         
        $fieldset->addElement( array( 'name' => 'start_date', 'label' => 'Start Date', 'type' => 'DateTime', 'value' => @$values['start_date'] ) );
        $fieldset->addElement( array( 'name' => 'expiry_date', 'label' => 'Coupon Expiry', 'type' => 'DateTime', 'value' => @$values['expiry_date'] ) );
        $fieldset->addElement( array( 'name' => 'maximum_usage', 'type' => 'InputText', 'label' => 'Maximum number of times this coupon can be used', 'placeholder' => 'e.g. 10', 'value' => @$values['maximum_usage'] ) );         
        $fieldset->addElement( array( 'name' => 'usage-x', 'label' => 'Current Usage', 'disabled' => true, 'type' => 'InputText', 'placeholder' => ' 0', 'value' => @$values['usage'] ) ); 

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
