<?php
namespace GaletteMaps\Controllers;

use Galette\Controllers\AbstractPluginController;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Contrôleur du plugin APHEC
 *
 * @category  Controllers
 * @name      ListsController
 * @package   Galette
 * @author    Florian Hatat
 * @copyright 2022 Florian Hatat
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 */

class ListsController extends AbstractPluginController
{
    /**
     * @Inject("Plugin APHEC pour Galette")
     * @var integer
     */
    protected $module_info;

    public function get_lists(Request $request, Response $response) : Response
    {
	return $response;
    }

    public function set_lists(Request $request, Response $response) : Response
    {
	return $response;
    }
}
