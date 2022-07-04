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
		$query = $this->zdb->db->query('
SELECT DISTINCT
  list.list_name AS list_name,
  sub.is_subscribed AS manual,
  prof.id_list AS automatic
FROM aphec_lists list
  LEFT JOIN (
	aphec_lists_profiles prof
	JOIN galette_dynamic_fields dfields
	ON prof.id_profile=dfields.field_val AND dfields.field_id=4 AND dfields.field_form="adh"
	   AND dfields.item_id=?
  ) ON list.id_list=prof.id_list
  LEFT JOIN aphec_lists_subscriptions sub ON sub.id_list=list.id_list AND sub.id_adh=?
ORDER BY list_name',
			[$this->login->id, $this->login->id]);

		$results = $this->zdb->execute($query);

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
				'list_name' => $row['list_name'],
				'status' => $status,
				'automatic_subscribed' => !is_null($row['automatic']),
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
		return $response;
	}
}
