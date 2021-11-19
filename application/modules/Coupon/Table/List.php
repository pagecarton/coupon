<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Coupon_Table_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Coupon_Table_List extends Coupon_Table_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Coupon Codes';   

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
      $this->setViewContent( $this->getList() );		
    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
							            'Creator' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Coupon_Table_Creator/\', \'page_refresh\' );" title="">Create a Coupon Code</a>',    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'code' => array( 'field' => 'code', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                    'value' => array( 'field' => 'value', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Int' ),                     
                    'type' => array( 'field' => 'type', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                 //   'product' => array( 'field' => 'product', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                //    'product_type' => array( 'field' => 'product_type', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                    'start_date' => array( 'field' => 'start_date', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ),                     
                    'expiry_date' => array( 'field' => 'expiry_date', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ),                     
                //    'maximum_usage' => array( 'field' => 'maximum_usage', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Int' ),                     
                    'usage' => array( 'field' => 'usage', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Int' ), 
                //    'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    '%FIELD% <a style="font-size:smaller;"  href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Coupon_Table_Editor/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>', 
                    '%FIELD% <a style="font-size:smaller;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Coupon_Table_Delete/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-trash" aria-hidden="true"></i></a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
