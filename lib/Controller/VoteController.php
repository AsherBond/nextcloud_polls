<?php
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

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;

use OCA\Polls\Service\VoteService;

class VoteController extends Controller {

	/** @var VoteService */
	private $voteService;

	use ResponseHandle;

	public function __construct(
		string $appName,
		IRequest $request,
		VoteService $voteService
	) {
		parent::__construct($appName, $request);
		$this->voteService = $voteService;
	}

	/**
	 * set
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function list(int $pollId): JSONResponse {
		return $this->response(fn () => ['votes' => $this->voteService->list($pollId)]);
	}

	/**
	 * set
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function set(int $optionId, string $setTo): JSONResponse {
		return $this->response(fn () => ['vote' => $this->voteService->set($optionId, $setTo)]);
	}

	/**
	 * Remove user from poll
	 * @NoAdminRequired
	 */
	public function delete(int $pollId, string $userId = ''): JSONResponse {
		return $this->response(fn () => ['deleted' => $this->voteService->delete($pollId, $userId)]);
	}
}
