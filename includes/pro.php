<?php
if ( !class_exists( 'TD_WebmasterUserRolePro' ) ) {
	class TD_WebmasterUserRolePro {
		function __construct( $parent ) {
			if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( dirname( __FILE__ ) ) . '/lib/redux/ReduxCore/framework.php' ) ) {
				require_once( dirname( dirname( __FILE__ ) ) . '/lib/redux/ReduxCore/framework.php' );
			}
			// if ( !isset( $redux_demo ) && file_exists( dirname( dirname( __FILE__ ) ) . '/lib/redux/sample/sample-config.php' ) ) {
			// 	require_once( dirname( dirname( __FILE__ ) ) . '/lib/redux/sample/sample-config.php' );
			// }
		}
	}
}