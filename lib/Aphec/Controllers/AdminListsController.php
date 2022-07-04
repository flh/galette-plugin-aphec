<?php
/**
 * Contrôleur du plugin APHEC
 *
 * @category  Controllers
 * @name	  AdminListsController
 * @package   Galette
 * @author	Florian Hatat
 * @copyright 2022 Florian Hatat
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 */

namespace Aphec\Controllers;

use Galette\Controllers\AbstractPluginController;
use Galette\Core\Db as GaletteDb;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminListsController extends AbstractPluginController
{
	/**
	 * @Inject("Plugin Aphec")
	 * @var integer
	 */
	protected $module_info;

	/**
	 * Trouver toutes les listes disponibles dans Sympa
	 */
	public function get_lists(Request $request, Response $response) : Response
	{
		// On commence par obtenir la liste de toutes les listes depuis Sympa
		$sympa_db = new GaletteDb([
			'TYPE_DB' => TYPE_DB,
			'HOST_DB' => HOST_DB,
			'PORT_DB' => PORT_DB,
			'USER_DB' => USER_DB,
			'PWD_DB' => PWD_DB,
			'NAME_DB' => 'sympa', // TODO get from config?
		]);
		$sympa_lists_rs = $sympa_db->db->query(
			"SELECT name_list FROM list_table WHERE status_list='open' AND name_list LIKE 'aphec-%'",
			\Laminas\Db\Adapter\Adapter::QUERY_MODE_EXECUTE
		);
		$sympa_lists = [];
		foreach($sympa_lists_rs as $sympa_list) {
			$sympa_lists[] = $sympa_list->name_list;
		}

		// Liste de toutes les matières possibles pour les adhérents
		/* TODO le nom de la table est codé en dur, il faudrait le retrouver
		   proprement avec l'API Galette. */
		$matieres_rs = $this->zdb->execute($this->zdb->select('field_contents_4'));
		$matieres = [];
		foreach($matieres_rs as $matiere) {
			$matieres[$matiere->id] = $matiere->val;
		}

		$aphec_lists_rs = $this->zdb->execute($this->zdb->select('aphec_lists')->order('sympa_name'));
		$aphec_lists = [];
		foreach($aphec_lists_rs as $aphec_list) {
			if(!in_array($aphec_list->sympa_name, $sympa_lists)) {
				// Suppressions des listes qui ne sont plus dans Sympa
				$query = $this->zdb->delete('aphec_lists')->where(function($w) use ($aphec_list) {
					$w->equalTo('id_list', $aphec_list->id_list);
				});
				$this->zdb->execute($query);
			}
			else {
				// Les listes connues sont à mettre dans le formulaire
				$aphec_lists[$aphec_list->id_list] = [
					"name" => $aphec_list->sympa_name,
					"description" => $aphec_list->sympa_description,
					"authorized" => intval(boolval($aphec_list->authorized)),
					"matieres" => [],
				];
			}
		}

		// Ajout des listes ajoutées dans Sympa et pas encore connues dans Galette
		$seen_aphec_lists = array_map(function ($item) { return $item['name']; }, $aphec_lists);
		foreach(array_diff($sympa_lists, $seen_aphec_lists) as $list_name) {
			$query = $this->zdb->insert('aphec_lists')->values([
				'sympa_name' => $list_name,
				'sympa_description' => '',
				'authorized' => 0,
			]);
			$entity = $this->zdb->execute($query);
			$list_id = $this->zdb->getLastGeneratedValue($entity);
			// Et on les déclare aussi pour le formulaire
			$aphec_lists[$list_id] = [
				"name" => $list_name,
				"description" => '',
				"authorized" => 0,
				"matieres" => [],
			];
		}

		$profiles_rs = $this->zdb->execute($this->zdb->select('aphec_lists_profiles'));
		foreach($profiles_rs as $profile) {
			$aphec_lists[$profile->id_list]["matieres"][] = $profile->id_profile;
		}

		$this->view->render(
			$response,
			'file:[' . $this->getModuleRoute() . ']admin_lists.tpl',
			[
				"aphec_lists" => $aphec_lists,
				"matieres" => $matieres,
				"page_title" => "Réglages des listes de diffusion",
			]
		);
		return $response;
	}

	/**
	 * Modifier les listes accessibles aux adhérents
	 */
	public function set_lists(Request $request, Response $response) : Response
	{
		$post = $request->getParsedBody();

		// Modifications à faire de façon atomique
		$this->zdb->connection->beginTransaction();

		$aphec_lists_rs = $this->zdb->execute($this->zdb->select('aphec_lists'));
		foreach($aphec_lists_rs as $aphec_list) {
			// Listes accessibles ou non
			$query = $this->zdb->update('aphec_lists')->set([
				'authorized' => ($post["list-{$aphec_list->id_list}-authorized"] ?? '') == 'on' ? 1 : 0,
			])->where(['id_list' => $aphec_list->id_list]);
			$this->zdb->execute($query);

			// Suppression des profils d'inscription désactivés
			$selected_profiles = [];
			foreach(($post["list-{$aphec_list->id_list}-profiles"] ?? []) as $profile_str) {
				// Filtre élémentaire pour retirer des valeurs soumises
				// aberrantes. Le gestionnaire de base de données est censé
				// faire le reste en vérifiant la clé étrangère id_profile.
				if(is_numeric($profile_str)) {
					$selected_profiles[] = intval($profile_str);
				}
			}
			// On retire tout, on ajoute les profils sélectionnés
			// au paragraphe suivant. Ceci évite de devoir faire
			// attention à éviter de ré-insérer les profils déjà
			// existants.
			$query = $this->zdb->delete('aphec_lists_profiles')->where(function($w) use ($aphec_list, $selected_profiles) {
				$w->equalTo('id_list', $aphec_list->id_list);
			});
			$this->zdb->execute($query);

			// Ajout des profils d'incription sélectionnés
			foreach($selected_profiles as $profile) {
				$query = $this->zdb->insert('aphec_lists_profiles')->values([
					'id_list' => $aphec_list->id_list,
					'id_profile' => $profile,
				]);
				$this->zdb->execute($query);
			}
		}

		// Fin de la transaction
		$this->zdb->connection->commit();

		return $response->withRedirect($this->router->pathFor('aphec_lists_admin'), 302);
	}
}
