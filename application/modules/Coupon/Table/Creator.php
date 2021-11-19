<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Coupon_Table_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Coupon_Table_Creator extends Coupon_Table_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Create a new Coupon Code'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			$this->createForm( 'Save...', 'Create Coupon' );
			$this->setViewContent( $this->getForm()->view() );

			if( ! $values = $this->getForm()->getValues() ){ return false; }

            $successMessage = 'Coupon code has been successfully added to the Coupon Management System. You can now begin to use this code to promote your products and services. Customers can use this code in two ways:

            1. Customers can enter this coupon code "' . $values['code'] . '" on checkout to get value of the coupon.
            2. Customer can use the following link to access the website, the coupon will be activated automatically when they get to the checkout: ' . Ayoola_Page::getHomePageUrl() . '?coupon=' . $values['code'] . '
            
            To manage all coupon codes and add new ones, visit ' . Ayoola_Page::getHomePageUrl() . '/widgets/Coupon_Table_List
            ';


			//	Notify Admin
			$mailInfo = array();
			$mailInfo['subject'] = 'Coupon Code Created Successfully';
			$mailInfo['body'] = $successMessage;
			try
			{
				@Ayoola_Application_Notification::mail( $mailInfo );
			}
			catch( Ayoola_Exception $e ){ null; }

            if( $this->insertDb( $values ) )
			{ 
				$this->setViewContent(  '' . self::__( '<p class="goodnews">Coupon created successfully. </p>' ) . '', true  ); 
				$this->setViewContent(  '<br><p class="">' . nl2br( $successMessage ) . '</p>' ); 
			}
            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
