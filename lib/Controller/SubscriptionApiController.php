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

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;

use OCP\IRequest;
use OCP\ILogger;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\Polls\Service\SubscriptionService;

class SubscriptionApiController extends ApiController {

	private $userId;
	private $subscriptionService;
	private $logger;

	/**
	 * SubscriptionController constructor.
	 * @param string $appName
	 * @param $UserId
	 * @param SubscriptionService $subscriptionService
	 * @param IRequest $request
	 * @param ILogger $logger
	 */

	public function __construct(
		string $appName,
		$userId,
		SubscriptionService $subscriptionService,
		IRequest $request,
		ILogger $logger

	) {
		parent::__construct($appName,
			$request,
			'PUT, GET, DELETE',
            'Authorization, Content-Type, Accept',
            1728000);
		$this->userId = $userId;
		$this->subscriptionService = $subscriptionService;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 * CORS
	 * @NoCSRFRequired
	 * @param integer $pollId
	 * @return DataResponse
	 */
	public function get($pollId) {
		try {
			return new DataResponse($this->subscriptionService->get($pollId), Http::STATUS_OK);
		} catch (NotAuthorizedException $e) {
			return new DataResponse('Unauthorized', Http::STATUS_FORBIDDEN);
		} catch (DoesNotExistException $e) {
			return new DataResponse('Not subscribed', Http::STATUS_NOT_FOUND);
		}
	}

	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 * @param integer $pollId
	 */
	public function subscribe($pollId) {
		try {
			return $this->subscriptionService->set($pollId, true);
			return new DataResponse('Subscribed', Http::STATUS_OK);
		} catch (NotAuthorizedException $e) {
			return new DataResponse('Unauthorized', Http::STATUS_FORBIDDEN);
		}
	}
	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 * @param integer $pollId
	 */
	public function unsubscribe($pollId) {
		try {
			$this->subscriptionService->set($pollId, false);
			return new DataResponse('Unsubscribed', Http::STATUS_OK);
		} catch (NotAuthorizedException $e) {
			return new DataResponse('Unauthorized', Http::STATUS_FORBIDDEN);
		}
	}
}
