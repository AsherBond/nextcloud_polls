<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Vinzenz Rosenkranz <vinzenz.rosenkranz@gmail.com>
 *
 * @author René Gieling <github@dartcafe.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Polls\Controller;

use OCA\Polls\Model\Acl;
use OCA\Polls\Service\CalendarService;
use OCA\Polls\Service\PreferencesService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

/**
 * @psalm-api
 */
class UserController extends BaseController {
	public function __construct(
		string $appName,
		IRequest $request,
		private PreferencesService $preferencesService,
		private CalendarService $calendarService,
		private Acl $acl,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Read all preferences
	 */
	#[NoAdminRequired]
	public function getPreferences(): JSONResponse {
		return $this->response(fn () => $this->preferencesService->get());
	}

	/**
	 * Write preferences
	 * @param array $preferences
	 */
	#[NoAdminRequired]
	public function writePreferences(array $preferences): JSONResponse {
		return $this->response(fn () => $this->preferencesService->write($preferences));
	}
	
	/**
	 * get acl for user
	 * @param $pollId Poll id
	 */
	#[NoAdminRequired]
	public function getAcl(): JSONResponse {
		return new JSONResponse(['acl' => $this->acl], Http::STATUS_OK);
	}


	/**
	 * Read all calendars
	 */
	#[NoAdminRequired]
	public function getCalendars(): JSONResponse {
		return new JSONResponse(['calendars' => $this->calendarService->getCalendars()], Http::STATUS_OK);
	}
}
