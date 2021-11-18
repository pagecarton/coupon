<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Coupon_Table
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Tuesday 16th of November 2021 09:22PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Coupon_Table extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.2';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'code' => 'INPUTTEXT',
  'value' => 'INPUTTEXT',
  'type' => 'INPUTTEXT',
  'product' => 'JSON',
  'product_type' => 'JSON',
  'start_date' => 'INPUTTEXT',
  'expiry_date' => 'INT',
  'maximum_usage' => 'INT',
  'usage' => 'INPUTTEXT',
);


	// END OF CLASS
}
