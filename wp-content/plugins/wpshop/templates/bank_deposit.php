<?php

	/*	Wordpress - Ajax functionnality activation	*/
	DEFINE('DOING_AJAX', true);
	/*	Wordpress - Main bootstrap file that load wordpress basic files	*/
	require_once('../../../../wp-load.php');
	/*	Wordpress - Admin page that define some needed vars and include file	*/
	require_once(ABSPATH . 'wp-admin/includes/admin.php');

	$company = get_option('wpshop_company_info', array());

	ob_start(); ?>
		<style>
			table {
				width: 100%;
				border-collapse: collapse;
			}
			.border {
				border: 1px solid #000;
			}
			.title {
				font-size: 20px;
			}
			.bold {
				font-weight: bold;
			}
			.width-100 {
				width: 100%;
			}
			.width-70 {
				width: 70%;
			}
			.width-15 {
				width: 15%;
			}
			.width-075 {
				width: 7.5%;
			}
			.margin-sides {
				padding: 0 1% 0 1%;
			}
			.force-one-line {
				white-space: nowrap;
 				overflow: hidden;
			}
			.valign-middle {
				vertical-align: middle;
			}
			.valign-top {
				vertical-align: top;
			}
			.valign-bottom {
				vertical-align: bottom;
			}
			.align-center {
				text-align: center;
			}
			.align-left {
				text-align: left;
			}
			.align-right {
				text-align: right;
			}
		</style>
	<?php
	$content_css = ob_get_contents();
	ob_end_clean();
	ob_start(); ?>
		<table cellspacing="0" style="width: 100%;border: 1px solid black;font-size: 11px;">
			<tr>
				<td style="width: 20%;"><?php echo $company['company_name']; ?></td>
				<td rowspan="4" style="width: 60%;text-align: center;font-size: 20px;font-weight: bold;"><?php _e('Bank deposit', 'wpshop'); ?></td>
				<td style="width: 7%;"><?php _e('SIRET', 'wpshop'); ?>:</td>
				<td style="width: 13%;text-align: right;"><?php echo $company['company_siret']; ?></td>
			</tr>
			<tr>
				<td style="width: 20%;"><?php echo $company['company_phone']; ?></td>
				<td style="width: 7%;"><?php _e('SIREN', 'wpshop'); ?>:</td>
				<td style="width: 13%;text-align: right;"><?php echo $company['company_siren']; ?></td>
			</tr>
			<tr>
				<td style="width: 20%;"><?php echo $company['company_street']; ?></td>
				<td style="width: 7%;vertical-align: top;"><?php _e('Date', 'wpshop'); ?>:</td>
				<td style="width: 13%;text-align: right;">
					<span style="white-space: nowrap;overflow: hidden;"><?php echo mysql2date( get_option( 'date_format' ), $_POST['date'], true ); ?></span>
				</td>
			</tr>
			<tr>
				<td style="width: 20%;"><?php echo $company['company_postcode'] . ' ' . $company['company_country']; ?></td>
				<td style="width: 7%;"></td>
				<td style="width: 13%;text-align: right;"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" style="width: 100%;font-size: 13px;">
			<tr>
				<th style="width: 8%;text-align: right;"><?php _e('Basic order', 'wpshop'); ?></th>
				<th style="width: 22%;text-align: center;"><?php _e('Date', 'wps-pos-i18n'); ?></th>
				<th style="width: 50%;text-align: center;"><?php _e('Products', 'wpshop'); ?></th>
				<th style="width: 20%;text-align: center;"><?php _e('Amount', 'wps-pos-i18n'); ?></th>
		 	</tr>
		</table>
		<table cellspacing="0" style="width: 100%;border: 1px solid black;font-size: 11px;">
		 	<?php foreach( json_decode( stripslashes( $_POST['payments'] ) )as $payment_received ) : ?>
		    <tr>
				<td style="width: 8%;padding: 0 1% 0 1%;text-align: right;"><?php echo $payment_received->order_key; ?></td>
				<td style="width: 22%;white-space: nowrap;overflow: hidden;padding: 0 1% 0 1%;text-align: center;font-size:10px;"><?php echo ( !empty($payment_received->date) ) ? mysql2date( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), $payment_received->date, true ) : ''; ?></td>
				<td style="width: 50%;white-space: nowrap;overflow: hidden;text-align: center;font-size:9px;"><?php
					if( isset( $payment_received->products ) ) {
						$first = true;
						foreach( $payment_received->products as $product ) {
							if( $first ) {
								$first = false;
							} else {
								echo ', ';
							}
							echo $product;
						}
					}
				?></td>
				<td style="width: 10%;text-align: left;padding: 0 1% 0 1%;"><?php _e( $payment_received->method, 'wpshop'); ?></td>
				<td style="width: 9%;text-align: right;padding: 0 1% 0 1%;"><?php echo $payment_received->amount; ?><?php echo wpshop_tools::wpshop_get_currency(); ?></td>
				<td style="width: 1%;"></td>
			</tr>
	   	 	<?php endforeach; ?>
	   	 </table>
	   	 <table cellspacing="0" style="width: 100%;font-size: 13px;font-weight: bold;border: solid 1px black;">
	   	 	<tr>
	   	 		<td style="width: 80%;"><?php _e('Bank deposit sum', 'wpshop'); ?></td>
	   	 		<td style="width: 19%;text-align: right;"><?php echo number_format( $_POST['amount'], 2, '.', '' ) . wpshop_tools::wpshop_get_currency(); ?></td>
				<td style="width: 1%;"></td>
	   	 	</tr>
		</table>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	
	if ( !empty( $_GET['mode'] ) && $_GET['mode'] == 'pdf') {
		require_once(WPSHOP_LIBRAIRIES_DIR.'HTML2PDF/html2pdf.class.php');
		try {
			$html_content = '<page>' . $content . '</page>';
			$html2pdf = new HTML2PDF('P', 'A4', 'fr');
	
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($html_content);
	
			$html2pdf->Output( __('Bank deposit', 'wpshop') . ' - ' . mysql2date( get_option( 'date_format' ), $_POST['date'] ) . '.pdf', 'D');
		}
		catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	} else { ?>
<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie8 wp-toolbar"  dir="ltr" lang="en-US">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" class="wp-toolbar"  dir="ltr" lang="en-US">
<!--<![endif]-->
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php _e('Bank deposit', 'wpshop'); ?> - <?php echo mysql2date( get_option( 'date_format' ), $_POST['date'] ); ?></title>
		<?php echo $content_css; ?>
	</head>
	<body>
		<?php echo $content; ?>
	</body>
</html>
	<?php
	}