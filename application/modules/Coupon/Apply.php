<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Coupon_Apply
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Apply.php Tuesday 16th of November 2021 09:25PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Coupon_Apply extends Coupon_Table_Abstract
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Apply a Coupon Code'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            //  Output demo content to screen
            $form = new Ayoola_Form( array( 'name' => __CLASS__ ) );
            $fieldset = new Ayoola_Form_Element();
            $previous = self::getObjectStorage( 'code' )->retrieve() ? : array();

            $fieldset->addElement( array( 'name' => 'code', 'type' => 'InputText', 'label' => 'Promo Code', 'placeholder' => 'Enter promo code here (If you have any)...', 'value' => @$previous[0]  ) );
            $fieldset->addRequirements( array( 'NotEmpty' => null ) );
            $form->submitValue = 'Apply';
            $form->addFieldset( $fieldset );
            $this->setViewContent( $form->view() ); 

            if( ! $values = $form->getValues() )
            {
                return false;
            }
            if( ! $coupon = Coupon_Table::getInstance()->selectOne( null, array( 'code' => $values['code'] ) ) )
            {
                $this->setViewContent( '<p class="badnews">Invalid Promo Code Entered. Please check the code and try again!</p>', true ); 
                $this->setViewContent( $form->view() ); 
                return false;
            }

            if( intval( $coupon['start_date'] ) > time() )
            {
                $this->setViewContent( '<p class="badnews">Promo Code is not yet active!</p>', true ); 
                $this->setViewContent( $form->view() ); 
                return false;
            }
            elseif( intval( $coupon['expiry_date'] ) < time() )
            {
                $this->setViewContent( '<p class="badnews">Promo Code has expired!</p>', true ); 
                $this->setViewContent( $form->view() ); 
                return false;
            }
            elseif( ! empty( $coupon['maximum_usage'] ) && $coupon['usage'] >= $coupon['maximum_usage'] )
            {
                $this->setViewContent( '<p class="badnews">Promo Code usage has been exhausted!</p>', true ); 
                $this->setViewContent( $form->view() ); 
                return false;
            }
               
            if( self::apply( $values['code'] ) )
            {
                $this->setViewContent( '<p class="goodnews">Promo code was applied successfully!</p>', true ); 
                $this->setViewContent( $form->view() ); 
                return true;
            }
            else
            {
                $this->setViewContent( '<p class="badnews">There was an error applying your promo code. Please contact customer care!</p>', true ); 
                $this->setViewContent( $form->view() ); 
                return false;
            }
            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //      Alert! Clear the all other content and display whats below.
            //      $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}

    
    /**
     * apply a code
     * 
     * param string coupon code
     */
	public static function apply( $code )
    {

        if( ! $coupon = Coupon_Table::getInstance()->selectOne( null, array( 'code' => $code ) ) )
        {
            return false;
        }

        if( intval( $coupon['start_date'] ) > time() )
        {
            return false;
        }
        elseif( intval( $coupon['expiry_date'] ) < time() )
        {
            return false;
        }
        elseif( $coupon['usage'] >= $coupon['maximum_usage'] )
        {
            return false;
        }

        $previous = self::getObjectStorage( 'code' )->retrieve() ? : array();
    
        if( 
            ! empty( $coupon['product'] ) 
            || ! empty( $coupon['product_type'] ) 
        )
        {
            //  always make the precise coupon to the top to allow it to be processed first
            array_unshift( $previous, $coupon['code'] );
        }
        else
        {
            $previous[] = $coupon['code'];
        }

        $previous = array_unique( $previous );
        //var_export( $previous );

        self::getObjectStorage( 'code' )->store( $previous );

        if( Application_Subscription::reset() )
        {
            $_GET['coupon_applied'] = $code;
            $_REQUEST['coupon_applied'] = $code;
            unset( $_GET['coupon'] );
            unset( $_REQUEST['coupon'] );
            header( 'Location: ' . Ayoola_Page::getCurrentUrl() );
        }
        return true;

    }

    
    /**
     * 
     * 
     * param array coupon code info
     */
	public static function removeCoupon( $coupon )
    {
        $previous = self::getObjectStorage( 'code' )->retrieve() ? : array();
        //var_export( $previous );
        foreach( $previous as $key => $each )
        {
            if( $each === $coupon['code'] )
            {
                unset( $previous[$key] );
                self::getObjectStorage( 'code' )->store( $previous );
                break;
            }
        }
        Application_Subscription::reset();


    }

    
    /**
     * apply a code
     * 
     * param array coupon code
     */
	public static function processCoupon( $coupon, & $cartData )
    {

        $cover = 'total order';
        $totalValue = $coupon['value'];

        $totalQualifyingProductPrice = $cartData['settings']['total'];

        if( ! empty( $coupon['product'] ) || ! empty( $coupon['product_type'] ) )
        {
            $totalQualifyingProductPrice = 0.00;

            foreach( $cartData['settings']['article_url'] as $each )
            {
                $record = Application_Article_Table::getInstance()->selectOne( null, array( 'article_url' => $each ) );
                if( 
                    ( is_array( $coupon['product'] ) && ! empty( $each ) && in_array( $each, $coupon['product'] ) )
                    || ( is_array( $coupon['product_type'] ) && ! empty( $record['article_type'] ) && in_array( $record['article_type'], $coupon['product_type'] ) ) 
                    || (  is_array( $coupon['product_type'] ) && ! empty( $record['true_post_type'] ) &&  in_array( $record['true_post_type'], $coupon['product_type'] ) ) )
                {
                    $totalQualifyingProductPrice += $cartData['cart'][$each]['item_total'];
                    $v[$each] = $record['article_title'];
                }
            }


            if( ! empty( $v ) )
            {  
                $cover = implode ( ' or ', $v );
            }

        }
        //var_export( $totalQualifyingProductPrice );


        $faceValue = $coupon['value'];

        $surchargeText = 'Promo Code ' . $coupon['code'];
        switch( $coupon['type'] )
        {
            case 'percentage':   
                $surchargeText = '- ' . $coupon['value'] . '% of ' . $cover . ' from pomo code ' . $coupon['code'] . '';
                $surchargePrice = ( $coupon['value']/100 ) * $totalQualifyingProductPrice;
            break;
            default:
                $surchargeText = '- ' . $coupon['value'] . ' from pomo code ' . $coupon['code'] . ' for ' . $cover;

                if( $totalQualifyingProductPrice < $faceValue )
                {
                    $faceValue = $totalQualifyingProductPrice;
                }
                $surchargeText = '- ' . $faceValue . ' from pomo code ' . $coupon['code'] . ' for ' . $cover;
                $surchargePrice = $faceValue;
            break;
        }


        @$coupon['price_id'] = $coupon['table_id'];	//	
        @$coupon['multiple'] = 1;	//	
        @$coupon['callback'] = __CLASS__;	//	
        @$coupon['refreshable'] = __METHOD__;	//	
        @$coupon['price'] = -$surchargePrice;	//	
        $coupon['subscription_name'] = $coupon['code'];
        $coupon['subscription_label'] = $surchargeText;
        $coupon['delete_method'] = __CLASS__ . '::removeCoupon';
        $coupon['url'] = '#';

        $cartData['cart'][$coupon['subscription_name']] = $coupon;
        $cartData['settings']['total'] -= $surchargePrice;
        //$class = new Application_Subscription();  

        //if( $class->subscribe( $coupon ) )
        {
            return true;
        }

	}


    /**
     * Performs funds transfer when user payment is completed
     * 
     * param array Order information
     */
	public static function hook(& $object, & $method, & $data )
    {
        $class = get_class( $object );

        if( $class === 'Ayoola_Page_Editor_Layout' )
        {
            switch( strtolower( $method ) )
            { 
                case 'getsitewidewidgets':
            
                    if( 
                        ! empty( $data['url'] ) 
                        && $data['url'] === '/cart' 
                        && $data['section'] === 'twosome1' 
                    )
                    {
        
                        $data['widgets'][] = array(
                            'parameters' => array(
                                'object_style' => 'padding:2em 0;'
                            ),
                            'class_name' => __CLASS__
                        );
                        
                    }
                    if( $data['section'] === 'middlebar' )
                    {
                        if( ! empty( $_REQUEST['coupon'] ) )
                        {
                            $code = strip_tags( $_REQUEST['coupon'] );

                            $message = '<p class="badnews">Promo code ' . $code .  ' could not be applied.</p>';
                            if( self::apply( $code ) )
                            {
                                $message = '<p class="goodnews">Promo code ' . $code .  ' applied successfully.</p>';
                            }
                        }
                        elseif( ! empty( $_REQUEST['coupon_applied'] ) )
                        {
                            $code = strip_tags( $_REQUEST['coupon_applied'] );
                            $previous = self::getObjectStorage( 'code' )->retrieve() ? : array();

                            $message = '<p class="badnews">Promo code ' . $code .  ' could not be applied.</p>';
                            if( in_array( $code, $previous ) )
                            {
                                $message = '<p class="goodnews">Promo code ' . $code .  ' applied successfully.</p>';
                            }
                        }
                        if( ! empty( $message ) )
                        {
                            $data['widgets'][] = array(
                                'parameters' => array(
                                    'codes' => $message
                                ),
                                'class_name' => 'Ayoola_Page_Editor_Text'
                            );
                        }
                    }
                break;
            }
        }
        elseif( $class === 'Application_Subscription' )
        {

            switch( strtolower( $method ) )
            { 
                case 'reset':
                    $previous = self::getObjectStorage( 'code' )->retrieve() ? : array();
                    foreach( $previous as $coupon )
                    {
                        if( ! $coupon = Coupon_Table::getInstance()->selectOne( null, array( 'code' => $coupon ) ) )
                        {
                            return false;
                        }
            
                        //var_export( $coupon );
                       // var_export( $data );
                        self::processCoupon( $coupon, $data );
                    }
                    
                break;
            }
        }


	}

    /**
     * Performs funds transfer when user payment is completed
     * 
     * param array Order information
     */
	public static function callback(& $orderInfo )
    {         
       
        if( ! empty( $orderInfo['code_used'] ) )
        {
            return true;
        }


        $where = array( 'code' => $orderInfo['code'] );
        $orderInfo['code_used'] = true;
        if( ! $coupon = Coupon_Table::getInstance()->selectOne( null, $where ) )
        {
            return false;
        }
        Coupon_Table::getInstance()->update( array( 'usage' => $coupon['usage'] + 1 ), $where );
        Coupon_Usage::getInstance()->insert( 
            array( 
                'email' => $orderInfo['full_order_info']['email'],
                'username' => $orderInfo['full_order_info']['username'],
                'code' => $orderInfo['code_used'],
            )
        );
        return true;
	}
	// END OF CLASS
}
