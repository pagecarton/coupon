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


class Coupon_Usage extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.5';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
        'code' => 'INPUTTEXT',
        'email' => 'INPUTTEXT',
        'username' => 'INPUTTEXT',
        'status' => 'INPUTTEXT',
        'user_duuid' => 'INPUTTEXT',
    );


	// END OF CLASS
}
