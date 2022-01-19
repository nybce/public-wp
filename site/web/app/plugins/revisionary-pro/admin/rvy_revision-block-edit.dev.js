/**
* Block Editor Modifications for Revisionary
*
* By Kevin Behrens
*
* Copyright 2021, PublishPress
*/
jQuery(document).ready( function($) {
	function RvyRecaptionElement(btnSelector, btnCaption, btnIcon = '') {
		if (rvyObjEdit.disableRecaption) {
			return;
		}
		
		let node = document.querySelector(btnSelector);

		if (node) {
			document.querySelector(btnSelector).innerText = `${btnCaption}`;

			if(btnIcon){
				document.querySelector(btnSelector).innerHTML = `<span class="dashicons dashicons-${btnIcon}"></span>${btnCaption}`;
			}
		}
	}

	// Update main publish ("Publish" / "Submit Pending") button width and span caption
	function RvySetPublishButtonCaption(caption,waitForSaveDraftButton,forceRegen,timeout) {
		if ('future' == rvyObjEdit.currentStatus) {
			caption = rvyObjEdit.updateCaption;
		}
		
		if ( caption == '' && ( typeof rvyObjEdit['publishCaptionCurrent'] != 'undefined' )  ) {
			caption = rvyObjEdit.publishCaptionCurrent;
		} else {
			rvyObjEdit.publishCaptionCurrent = caption;
		}

		if ( typeof waitForSaveDraftButton == 'undefined' ) {
			waitForSaveDraftButton = false;
		}

		if ( ( ! waitForSaveDraftButton || $('button.editor-post-switch-to-draft').filter(':visible').length || $('button.editor-post-save-draft').filter(':visible').length ) && $('button.editor-post-publish-button').length ) {  // indicates save operation (or return from Pre-Publish) is done
			RvyRecaptionElement('button.editor-post-publish-button', caption);
		}
	}

	// Force spans to be regenerated following modal settings window access
	var RvyDetectPublishOptionsDivClosureInterval='';
	var RvyDetectPublishOptionsDiv = function() {
		if ( $('div.components-modal__header').length ) {
			clearInterval( RvyDetectPublishOptionsDivInterval );

			if ( $('span.pp-recaption-button').first() ) {
				rvyObjEdit.overrideColor = $('span.pp-recaption-button').first().css('color');
			}

			var RvyDetectPublishOptionsClosure = function() {
				if ( ! $('div.components-modal__header').length ) {
					clearInterval(RvyDetectPublishOptionsDivClosureInterval);

					$('span.pp-recaption-button').hide(); //.addClass('force-regen');
					RvyInitInterval = setInterval(RvyInitializeBlockEditorModifications, 50);
					RvyDetectPublishOptionsDivInterval = setInterval(RvyDetectPublishOptionsDiv, 1000);
				}
			}
			RvyDetectPublishOptionsDivClosureInterval = setInterval(RvyDetectPublishOptionsClosure, 200);
		}
	}
	var RvyDetectPublishOptionsDivInterval = setInterval(RvyDetectPublishOptionsDiv, 1000);


	/************* RECAPTION PRE-PUBLISH AND PUBLISH BUTTONS ****************/
	rvyObjEdit.publishCaptionCurrent = rvyObjEdit.publish;

	// Initialization operations to perform once React loads the relevant elements
	var RvyInitializeBlockEditorModifications = function() {
		if ( ( $('button.editor-post-publish-button').length || $('button.editor-post-publish-panel__toggle').length ) && ( $('button.editor-post-switch-to-draft').length || $('button.editor-post-save-draft').length ) ) {
			clearInterval(RvyInitInterval);

			if ( $('button.editor-post-publish-panel__toggle').length ) {
				if ( typeof rvyObjEdit.prePublish != 'undefined' && rvyObjEdit.prePublish ) {
					RvyRecaptionElement('button.editor-post-publish-panel__toggle', rvyObjEdit.prePublish);
				}

				// Presence of pre-publish button means publish button is not loaded yet. Start looking for it once Pre-Publish button is clicked.
				$(document).on('click', 'button.editor-post-publish-panel__toggle,span.pp-recaption-prepublish-button', function() {
					RvySetPublishButtonCaption('', false, true); // nullstring: set caption to value queued in rvyObjEdit.publishCaptionCurrent
				});
			} else {
				RvySetPublishButtonCaption(rvyObjEdit.publish, false, true);
			}

			$('select.editor-post-author__select').parent().hide();
			$('button.editor-post-trash').parent().show();
			$('button.editor-post-switch-to-draft').hide();

			$('div.components-notice-list').hide();	// autosave notice
		}

		if ( ( $('button.editor-post-publish-button').length || $('button.editor-post-publish-panel__toggle').length ) ) {
			$('button.editor-post-publish-button').hide();
			$('button.editor-post-publish-panel__toggle').hide();
		}
	}
	var RvyInitInterval = setInterval(RvyInitializeBlockEditorModifications, 50);

	var RvyHideElements = function() {
		var ediv = 'div.edit-post-sidebar ';

		if ($(ediv + 'div.edit-post-post-visibility,' + ediv + 'div.editor-post-link,' + ediv + 'select.editor-post-author__select:visible,' + ediv + 'div.components-base-control__field input[type="checkbox"]:visible,' + ediv + 'button.editor-post-switch-to-draft,' + ediv + 'button.editor-post-trash').length ) {
			$(ediv + 'select.editor-post-author__select').parent().hide();
			$(ediv + 'button.editor-post-trash').parent().show();
			$(ediv + 'button.editor-post-switch-to-draft').hide();
			$(ediv + 'div.editor-post-link').parent().hide();
			$(ediv + 'div.components-notice-list').hide();	// autosave notice

			if (!rvyObjEdit.scheduledRevisionsEnabled) {
				$(ediv + 'div.edit-post-post-schedule').hide();
			}

			$(ediv + '#publishpress-notifications').hide();

			$('#icl_div').closest('div.edit-post-meta-boxes-area').hide();
		}

		if ('future' == rvyObjEdit.currentStatus) {
			$('button.editor-post-publish-button').show();
		
		} else {
			if ( $('button.editor-post-publish-button').length ) {
				$('button.editor-post-publish-button').hide();
			}
		}
	}
	var RvyHideInterval = setInterval(RvyHideElements, 50);

	var RvySubmissionUI = function() {
		// @todo: use .edit-post-post-visibility if edit-post-post-schedule not available
		if ($('div.edit-post-post-schedule').length) {
			var refSelector = 'div.edit-post-post-schedule';
		} else {
			var refSelector = 'div.edit-post-post-visibility';
		}

		if (rvyObjEdit.ajaxurl && !$('div.edit-post-revision-status').length && $(refSelector).length) {
			$(refSelector).before(
				'<div class="components-panel__row rvy-creation-ui edit-post-revision-status">'
				+ '<span>' + rvyObjEdit.statusLabel + '</span>'
				+ '<div class="components-dropdown rvy-current-status">'
				+ rvyObjEdit[rvyObjEdit.currentStatus + 'StatusCaption']
				+ '</div>'
				+ '</div>'
			);

			if (rvyObjEdit[rvyObjEdit.currentStatus + 'ActionURL']) {
				var url = rvyObjEdit[rvyObjEdit.currentStatus + 'ActionURL'];
			} else {
				var url = 'javascript:void(0)';
			}

			if (rvyObjEdit[rvyObjEdit.currentStatus + 'ActionCaption']) {
				$(refSelector).after(
					'<div class="rvy-creation-ui"><a href="' + url + '" class="revision-approve">'
					+ '<button type="button" class="components-button revision-approve is-button is-primary ppr-purple-button">'
					+ '<span class="dashicons dashicons-yes"></span>'
					+ rvyObjEdit[rvyObjEdit.currentStatus + 'ActionCaption'] + '</button></a>'

					+ '<div class="revision-submitting" style="display: none;">'
					+ '<span class="revision-approve revision-submitting">'
					+ rvyObjEdit[rvyObjEdit.currentStatus + 'InProcessCaption'] + '</span><span class="spinner ppr-submission-spinner" style=""></span></div>'

					+ '<div class="revision-created" style="display: none">'
					+ '<span class="revision-approve revision-created">'
					+ rvyObjEdit[rvyObjEdit.currentStatus + 'CompletedCaption'] + '</span> '

					+ '<a href="' + rvyObjEdit[rvyObjEdit.currentStatus + 'CompletedURL'] + '" class="revision-approve revision-edit components-button is-secondary ppr-purple-button" target="pp_revisions_copy">'
					+ rvyObjEdit[rvyObjEdit.currentStatus + 'CompletedLinkCaption'] + '</a></div>'

					+ '</div>'
				);
			}

			if (RvyApprovalLocked != $('button.revision-approve').prop('disabled')) {
				if (RvyApprovalLocked) {
					$('button.revision-approve').html('Revision needs update.');
				} else {
					$('button.revision-approve').html(rvyObjEdit[rvyObjEdit.currentStatus + 'ActionCaption']);
				}
			}

			$('button.revision-approve').prop('disabled', RvyApprovalLocked && ('pending' == rvyObjEdit.currentStatus));

			$('.edit-post-post-schedule__toggle').after('<button class="components-button is-tertiary post-schedule-footnote" disabled>' + rvyObjEdit.onApprovalCaption + '</button>');

			if (rvyObjEdit[rvyObjEdit.currentStatus + 'DeletionURL']) {
				$('button.editor-post-trash').wrap('<a href="' + rvyObjEdit[rvyObjEdit.currentStatus + 'DeletionURL'] + '" style="text-decoration:none"></a>');
			}
		}

		$('button.post-schedule-footnote').toggle(!/\d/.test($('button.edit-post-post-schedule__toggle').html()));
	}
	var RvyUIInterval = setInterval(RvySubmissionUI, 100);


	setInterval(function() {
		if (rvyObjEdit.deleteCaption && $('button.editor-post-trash').length && ($('button.editor-post-trash').html() != rvyObjEdit.deleteCaption)) {
			$('button.editor-post-trash').html(rvyObjEdit.deleteCaption).closest('div').show();
		}
	}, 100);

	var RvyApprovalLocked = false;

	$(document).on('click', 'div.edit-post-visual-editor *, div.editor-inserter *', function() {
		if ('pending' == rvyObjEdit.currentStatus) {
			RvyApprovalLocked = true;
			$('button.revision-approve').prop('disabled', true);
		}
	});

	$(document).on('click', 'button.edit-post-post-schedule__toggle', function() {
		RvyApprovalLocked = true;
		$('button.revision-approve').prop('disabled', true);
	});

	$(document).on('click', 'button.editor-post-save-draft', function() {
		RvyApprovalLocked = false;
		$('button.revision-approve').prop('disabled', false);
		$('button.revision-approve').html(rvyObjEdit[rvyObjEdit.currentStatus + 'ActionCaption']);
	});

	function RvyGetRandomInt(max) {
		return Math.floor(Math.random() * max);
	}

	$(document).on('click', 'div.postbox-container', function() {
		$('button.revision-approve').prop('disabled', 'disabled');
	});

	var rvyIsAutosaveStarted = false;
	var rvyIsAutosaveDone = false;

	$(document).on('click', 'button.revision-approve', function() {
		// If autosave approvals are ever enabled, we will need this
		var isSubmission = (rvyObjEdit[rvyObjEdit.currentStatus + 'ActionURL'] == "");

		$('button.revision-approve').hide();
		$('div.revision-submitting').show().css('display', 'block');
		$('div.revision-submitting span.ppr-submission-spinner').css('visibility', 'visible');

		if (!wp.data.select('core/editor').isEditedPostDirty()) {
			if (isSubmission) {
				rvySubmitCopy();
			}

			return;
		}

		rvyIsAutosaveStarted = false;
		rvyIsAutosaveDone = false;

		wp.data.dispatch('core/editor').autosave();

		var tmrNoAutosave = setTimeout(() => {
			if (!rvyIsAutosaveStarted) {
				clearInterval(intAutosaveWatch);

				if (isSubmission) {
					rvySubmitCopy();
				}
			}
		}, 10000);

		var intAutosaveDoneWatch;

		var intAutosaveWatch = setInterval(() => {
			if (wp.data.select('core/editor').isAutosavingPost()) {
				rvyIsAutosaveStarted = true; 
				clearInterval(intAutosaveWatch);
				clearTimeout(tmrNoAutosave);

				var tmrAutosaveTimeout = setTimeout(() => {
					if (!rvyIsAutosaveDone) {
						clearInterval(intAutosaveWatch);

						if (isSubmission) {
							rvySubmitCopy();
						}
					}
				}, 10000);

				intAutosaveDoneWatch = setInterval(() => {
					if (!wp.data.select('core/editor').isAutosavingPost()) {
						rvyIsAutosaveDone = true;
						clearInterval(intAutosaveDoneWatch);
						clearTimeout(tmrAutosaveTimeout);

						if (isSubmission) {
							rvySubmitCopy();
						}
					}
				}, 100);
			}
		}, 100);
	});

	function rvySubmitCopy() {
		var revisionaryCreateDone = function () {
			$('.revision-approve').hide();
			$('div.revision-submitting').hide();
			$('.revision-created').show();

			// @todo: abstract this for other workflows
			rvyObjEdit.currentStatus = 'pending';

			$('.rvy-current-status').html(rvyObjEdit[rvyObjEdit.currentStatus + 'StatusCaption']);
			$('a.revision-edit').attr('href', rvyObjEdit[rvyObjEdit.currentStatus + 'CompletedURL']).show();
		}

		var revisionaryCreateError = function (data, txtStatus) {
			$('div.rvy-creation-ui').html(rvyObjEdit[rvyObjEdit.currentStatus + 'ErrorCaption']);
		}

		var data = {'rvy_ajax_field': rvyObjEdit[rvyObjEdit.currentStatus + 'AjaxField'], 'rvy_ajax_value': wp.data.select('core/editor').getCurrentPostId(), 'nc': RvyGetRandomInt(99999999)};

		$.ajax({
			url: rvyObjEdit.ajaxurl,
			data: data,
			dataType: "html",
			success: revisionaryCreateDone,
			error: revisionaryCreateError
		});
	}

	var RvyRecaptionSaveDraft = function() {
		if ($('button.editor-post-save-draft:not(.rvy-recaption)').length) {
			RvyRecaptionElement('button.editor-post-save-draft:not(.rvy-recaption)', rvyObjEdit.saveRevision);

			$('button.editor-post-save-draft:not(.rvy-recaption)').addClass('rvy-recaption').removeClass('is-tertiary').addClass('is-primary').addClass('ppr-purple-button');
		}

		if (($('div.edit-post-header__settings a.editor-post-preview:visible').length || $('div.block-editor-post-preview__dropdown button.block-editor-post-preview__button-toggle:visible').length) && !$('a.rvy-post-preview').length) {

			if (rvyObjEdit.viewURL && $('.block-editor-post-preview__button-toggle').length) {

				if ($('div.edit-post-header-preview__grouping-external').length == 1) {
					var svgElem = $('div.edit-post-header-preview__grouping-external a svg').clone()[0].outerHTML;

					$('div.edit-post-header-preview__grouping-external').after(
						'<div class="components-menu-group"><div role="group"><div class="edit-post-header-preview__grouping-external">'
						+ '<a href="' + rvyObjEdit.viewURL + '" target="pp_revisions_copy" role="menuitem" class="components-button edit-post-header-preview__button-external">'
						+ rvyObjEdit.viewTitle + svgElem + '</a></div></div></div>'
					);
				}

				if (rvyObjEdit.viewCaption) {
					RvyRecaptionElement('.block-editor-post-preview__button-toggle', rvyObjEdit.viewCaption);
					$('button.block-editor-post-preview__button-toggle:not(.ppr-purple-button)').removeClass('is-tertiary').addClass('is-secondary').addClass('ppr-purple-button');
				}			
			}

			if (rvyObjEdit.viewTitle) {
				$('div.edit-post-header__settings a.rvy-post-preview').attr('title', rvyObjEdit.viewTitle);
			}
			
		} else {
			if (!rvyObjEdit.multiPreviewActive) { // WP < 5.5
				if (!$('a.editor-post-preview').next('a.rvy-post-preview').length) {
					original = $('div.edit-post-header__settings a.editor-post-preview');
					$(original).after(original.clone().attr('href', rvyObjEdit.viewURL).attr('target', '_blank').removeClass('editor-post-preview').addClass('rvy-post-preview').css('margin', '0 10px 0 10px'));

					if (rvyObjEdit.viewCaption) {
						RvyRecaptionElement('div.edit-post-header__settings a.rvy-post-preview', rvyObjEdit.viewCaption);
					}
		
					if (rvyObjEdit.viewTitle) {
						$('div.edit-post-header__settings a.rvy-post-preview').attr('title', rvyObjEdit.viewTitle);
					}
				}

				if (rvyObjEdit.previewTitle && !$('a.editor-post-preview').attr('title')) {
					$('div.edit-post-header__settings a.editor-post-preview').attr('title', rvyObjEdit.previewTitle);
				}
			}
		}

		if (rvyObjEdit.revisionEdits && $('div.edit-post-sidebar a.editor-post-last-revision__title:visible').length && !$('div.edit-post-sidebar a.editor-post-last-revision__title.rvy-recaption').length) {
			$('div.edit-post-sidebar a.editor-post-last-revision__title').html(rvyObjEdit.revisionEdits);
			$('div.edit-post-sidebar a.editor-post-last-revision__title').addClass('rvy-recaption');
		}
	}
	var RvyRecaptionSaveDraftInterval = setInterval(RvyRecaptionSaveDraft, 100);
});
