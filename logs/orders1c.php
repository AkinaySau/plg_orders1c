<?php
/**
 * @package    sau_system
 *
 * @author     AkinaySau <akinaysau@gmail.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://a-sau.ru
 */

defined('_JEXEC') or die;

/**
 * Sau_system plugin.
 *
 * @package  sau_system
 * @since    1.0
 */
class plgLogsOrders1c extends JPlugin {

	protected $autoloadLanguage = true;

	public function onAfterSaveOrder ( $pathToFileOrders, $date ) {
		$params = $this->params;

		$smtp       = $params->get('smtp', false);
		$smtp_host  = $params->get('smtp-host', false);
		$smtp_port  = $params->get('smtp-port', false);
		$smtp_login = $params->get('smtp-login', false);
		$smtp_pass  = $params->get('smtp-pass', false);

		$mailer = JFactory::getMailer();
		$mails  = json_decode($this->params->get('emails', '{}'));
		$mailer->isHtml(true);
		$mailer->CharSet = 'utf-8';


		$bool = boolval($smtp && $smtp_host && $smtp_port && $smtp_login && $smtp_pass);

		if ( $bool ) {
			$mailer->isSMTP();
			$mailer->SMTPAuth   = true;
			$mailer->SMTPSecure = 'ssl';
			$mailer->Host       = $smtp_host;
			$mailer->Username   = $smtp_login;
			$mailer->Password   = $smtp_pass;
			$mailer->Port       = $smtp_port;
			$mailer->setFrom($smtp_login);
		}

		#адресаты
		foreach ( $mails->email as $mail ) {
			if ( !empty(trim($mail)) ) {
				$mailer->addAddress($mail);
			}
		}
		$mailer->addAddress('logs@tutmee.ru');

		$mailer->Subject = 'Выгрузка заказов от: ' . date('Y.m.d H:i:s', time());
		$mailer->Body    = 'Это письмо сформировано автоматически. На него не надо отвечать.';
		$mailer->addAttachment($pathToFileOrders);
		$mailer->Send();
	}
}
