<?php
/**
 * Contrôleur pour la gestion des inscriptions par un adhérent
 *
 * @category  Controllers
 * @name	  ListsController
 * @package   Galette
 * @author	Florian Hatat
 * @copyright 2022 Florian Hatat
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 */

namespace Aphec\Controllers;

use Galette\Controllers\AbstractPluginController;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ListsController extends AbstractPluginController
{
	/**
	 * @Inject("Plugin Aphec")
	 * @var integer
	 */
	protected $module_info;

	/**
	 * Trouver toutes les listes auxquelles un adhérent peut s'abonner
	 */
	public function get_lists(Request $request, Response $response) : Response
	{
		// Récupération de la liste des abonnements pour l'adhérent
		$results = $this->zdb->db->query('
SELECT DISTINCT
  list.sympa_name AS list_name,
  list.sympa_description AS list_description,
  sub.is_subscribed AS manual,
  prof.id_list AS automatic
FROM galette_aphec_lists list
  LEFT JOIN (
	galette_aphec_lists_profiles prof
	JOIN galette_dynamic_fields dfields
	ON prof.id_profile=dfields.field_val AND dfields.field_id=4 AND dfields.field_form="adh"
	   AND dfields.item_id=?
  ) ON list.id_list=prof.id_list
  LEFT JOIN galette_aphec_lists_subscriptions sub ON sub.id_list=list.id_list AND sub.id_adh=?
WHERE list.authorized
ORDER BY list_name',
			[$this->login->id, $this->login->id]);

		$list_subscriptions = [];
		foreach($results as $row) {
			if(is_null($row->manual)) {
				$status = 'automatic';
			}
			elseif($row->manual) {
				$status = 'opt-in';
			}
			else {
				$status = 'opt-out';
			}
			$list_subscriptions[] = [
				'list_name' => $row->list_name,
				'list_description' => $row->list_description,
				'status' => $status,
				'automatic_subscribed' => !is_null($row->automatic),
			];
		}

		$this->view->render(
			$response,
			'file:[' . $this->getModuleRoute() . ']member_lists.tpl', [
				'list_subscriptions' => $list_subscriptions,
				'page_title' => 'Inscriptions aux listes de diffusion',
			]
		);

		return $response;
	}

	/**
	 * Modifier les listes auxquelles un adhérent est abonné.
	 */
	public function set_lists(Request $request, Response $response) : Response
	{
		// On récupère d'abord les listes ouvertes à l'inscription
		$query = $this->zdb->select('aphec_lists')->where(['authorized' => 1]);
		$lists_rs = $this->zdb->execute($query);

		$post = $request->getParsedBody();

		// Pour chacune, on enregistre l'état d'inscription demandé par
		// l'adhérent. On se place dans une transaction afin de pouvoir
		// tranquillement supprimer toutes les lignes anciennes et ajouter les
		// nouvelles ensuite, de manière atomique.
		$this->zdb->connection->beginTransaction();
		$query = $this->zdb->delete('aphec_lists_subscriptions')->where([
			'id_adh' => $this->login->id]);
		$this->zdb->execute($query);
		foreach($lists_rs as $list) {
			$mode = $post["subscription-{$list->sympa_name}"] ?? null;
			if($mode === 'optin') {
				$query = $this->zdb->insert('aphec_lists_subscriptions')->values([
					'id_adh' => $this->login->id,
					'id_list' => $list->id_list,
					'is_subscribed' => 1,
				]);
				$this->zdb->execute($query);
			}
			elseif($mode === 'optout') {
				$query = $this->zdb->insert('aphec_lists_subscriptions')->values([
					'id_adh' => $this->login->id,
					'id_list' => $list->id_list,
					'is_subscribed' => 0,
				]);
				$this->zdb->execute($query);
			}
			elseif($mode === 'automatic') {
				// Rien pour l'instant, on supprimé toutes les lignes plus haut.
				// TODO il ne faudrait supprimer que les lignes mentionnées en "automatic" et laisser les autres intactes, pour permettre des changements partiels.
			}
		}
		$this->zdb->connection->commit();

		return $response->withRedirect($this->router->pathFor('aphec_lists_get'), 302);
	}
}
