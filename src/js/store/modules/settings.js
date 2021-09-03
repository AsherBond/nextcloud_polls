/*
 * @copyright Copyright (c) 2019 Rene Gieling <github@dartcafe.de>
 *
 * @author Rene Gieling <github@dartcafe.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

const defaultSettings = () => ({
	user: {
		experimental: false,
		calendarPeek: false,
		checkCalendars: [],
		useImage: false,
		imageUrl: '',
		glassyNavigation: false,
		glassySidebar: false,
		defaultViewTextPoll: 'list-view',
		defaultViewDatePoll: 'table-view',
		performanceThreshold: 1000,
	},
	session: {
		manualViewDatePoll: '',
		manualViewTextPoll: '',
	},
	availableCalendars: [],
	viewModes: [
		'list-view',
		'table-view',
	],
})

const state = defaultSettings()
const namespaced = true

const mutations = {
	reset(state) {
		Object.assign(state, defaultSettings())
	},

	setPreference(state, payload) {

		if (payload.defaultViewTextPoll === 'desktop') {
			payload.defaultViewTextPoll = 'table-view'
		}
		if (payload.defaultViewTextPoll === 'mobile') {
			payload.defaultViewTextPoll = 'list-view'
		}
		if (payload.defaultViewDatePoll === 'desktop') {
			payload.defaultViewDatePoll = 'table-view'
		}
		if (payload.defaultViewDatePoll === 'mobile') {
			payload.defaultViewDatePoll = 'list-view'
		}

		Object.keys(payload).filter((key) => key in state.user).forEach((key) => {
			state.user[key] = payload[key]
		})
	},

	setCalendars(state, payload) {
		state.availableCalendars = payload.calendars
	},

	addCheckCalendar(state, payload) {
		state.user.checkCalendars.push(payload.calendar.key)
	},

	setViewDatePoll(state, payload) {
		state.session.manualViewDatePoll = payload
	},
	setViewTextPoll(state, payload) {
		state.session.manualViewTextPoll = payload
	},
}

const getters = {
	viewTextPoll(state) {
		if (state.session.manualViewTextPoll) {
			return state.session.manualViewTextPoll
		}
		if (window.innerWidth > 480) {
			return state.user.defaultViewTextPoll
		}
		return 'list-view'

	},

	viewDatePoll(state) {
		if (state.session.manualViewDatePoll) {
			return state.session.manualViewDatePoll
		}
		if (window.innerWidth > 480) {
			return state.user.defaultViewDatePoll
		}
		return 'list-view'

	},
}

const actions = {
	async get(context) {
		const endPoint = 'apps/polls/preferences'
		try {
			const response = await axios.get(generateUrl(endPoint), { params: { time: +new Date() } })
			if (response.data.preferences.defaultViewTextPoll === 'desktop') {
				response.data.preferences.defaultViewTextPoll = 'table-view'
			}
			if (response.data.preferences.defaultViewTextPoll === 'mobile') {
				response.data.preferences.defaultViewTextPoll = 'list-view'
			}
			if (response.data.preferences.defaultViewDatePoll === 'desktop') {
				response.data.preferences.defaultViewDatePoll = 'table-view'
			}
			if (response.data.preferences.defaultViewDatePoll === 'mobile') {
				response.data.preferences.defaultViewDatePoll = 'list-view'
			}
			context.commit('setPreference', response.data.preferences)
		} catch {
			context.commit('reset')
		}
	},

	async write(context) {
		const endPoint = 'apps/polls/preferences'
		try {
			const response = await axios.post(generateUrl(endPoint), { settings: context.state.user })
			context.commit('setPreference', response.data.preferences)
		} catch (e) {
			console.error('Error writing preferences', { error: e.response }, { preferences: state.user })
			throw e
		}
	},

	async getCalendars(context) {
		const endPoint = 'apps/polls/calendars'
		const response = await axios.get(generateUrl(endPoint))
		context.commit('setCalendars', { calendars: response.data.calendars })
		return response
	},
}

export default { namespaced, state, mutations, getters, actions }
