<!--
  - SPDX-FileCopyrightText: 2021 Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div class="action toggle-sidebar">
		<NcButton type="primary"
			:aria-label="caption"
			@click="clickAction()">
			{{ caption }}
		</NcButton>
	</div>
</template>

<script>
import { NcButton } from '@nextcloud/vue'
import { emit } from '@nextcloud/event-bus'

export default {
	name: 'ActionOpenOptionsSidebar',

	components: {
		NcButton,
	},

	data() {
		return {
			caption: t('polls', 'Add some!'),
		}
	},

	methods: {
		clickAction() {
			emit('polls:sidebar:toggle', { open: true, activeTab: 'options' })
		},
	},
}
</script>
