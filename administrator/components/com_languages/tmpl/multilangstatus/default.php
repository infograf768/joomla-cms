<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$notice_homes     = $this->homes == 2 || $this->homes == 1 || $this->homes - 1 != count($this->contentlangs) && ($this->language_filter || $this->switchers != 0) && count($this->site_langs) == count($this->contentlangs);
$notice_disabled  = !$this->language_filter && ($this->homes > 1 || $this->switchers != 0);
$notice_switchers = !$this->switchers && ($this->homes > 1 || $this->language_filter);

// Defining arrays
$content_languages = array_column($this->contentlangs, 'lang_code');
$sitelangs         = array_column($this->site_langs, 'element');
$home_pages        = array_column($this->homepages, 'language');
?>
<div class="mod-multilangstatus">
	<?php if (!$this->language_filter && $this->switchers == 0) : ?>
		<?php if ($this->homes == 1) : ?>
			<div class="alert alert-info">
				<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_NONE'); ?>
			</div>
		<?php else : ?>
			<div class="alert alert-info">
				<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_USELESS_HOMES'); ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
	<table class="table table-sm">
		<tbody>
		<?php if ($this->defaultHome == true) : ?>
			<tr class="table-warning">
				<td>
					<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
					<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
				</td>
				<td>
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_DEFAULT_HOME_MODULE_PUBLISHED'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($notice_homes) : ?>
			<tr class="table-warning">
				<td>
					<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
					<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
				</td>
				<td>
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_HOMES_MISSING'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php foreach ($this->statuses as $status) : ?>
			<?php // Displays error when the Content Language is trashed ?>
			<?php if ($status->element && $status->published == -2) : ?>
				<tr class="table-warning">
					<td>
						<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
						<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
					</td>
					<td>
						<?php echo Text::sprintf('COM_LANGUAGES_MULTILANGSTATUS_CONTENT_LANGUAGE_TRASHED', $status->element); ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php // Displays error when both Content Language and Home page are unpublished ?>
			<?php if ($status->lang_code && $status->published == 0 && !$status->home_language) : ?>
				<tr class="table-warning">
					<td>
						<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
						<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
					</td>
					<td>
						<?php echo Text::sprintf('COM_LANGUAGES_MULTILANGSTATUS_CONTENT_LANGUAGE_HOME_UNPUBLISHED', $status->lang_code, $status->lang_code); ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($notice_disabled) : ?>
			<tr class="table-warning">
				<td>
					<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
					<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
				</td>
				<td>
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_LANGUAGEFILTER_DISABLED'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($notice_switchers) : ?>
			<tr class="table-warning">
				<td>
					<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
					<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
				</td>
				<td>
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_LANGSWITCHER_UNPUBLISHED'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php foreach ($this->contentlangs as $contentlang) : ?>
			<?php if (array_key_exists($contentlang->lang_code, $this->homepages) && (!array_key_exists($contentlang->lang_code, $this->site_langs) || $contentlang->published != 1)) : ?>
				<tr class="table-warning">
					<td>
						<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
						<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
					</td>
					<td>
						<?php echo Text::sprintf('COM_LANGUAGES_MULTILANGSTATUS_ERROR_CONTENT_LANGUAGE', $contentlang->lang_code); ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if (!array_key_exists($contentlang->lang_code, $this->site_langs)) : ?>
				<tr class="table-warning">
					<td>
						<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
						<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
					</td>
					<td>
						<?php echo Text::sprintf('COM_LANGUAGES_MULTILANGSTATUS_ERROR_LANGUAGE_TAG', $contentlang->lang_code); ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($this->listUsersError) : ?>
			<tr class="info">
				<td>
					<span class="fa fa-help" aria-hidden="true"></span>
					<span class="sr-only"><?php echo Text::_('JHELP'); ?></span>
				</td>
				<td>
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_CONTACTS_ERROR_TIP'); ?>
					<ul>
					<?php foreach ($this->listUsersError as $user) : ?>
						<li>
						<?php echo Text::sprintf('COM_LANGUAGES_MULTILANGSTATUS_CONTACTS_ERROR', $user->name); ?>
						</li>
					<?php endforeach; ?>
					</ul>
				</td>
			</tr>
		<?php endif; ?>
		<?php // Displays error when the Content Language has been deleted ?>
		<?php foreach ($sitelangs as $sitelang) : ?>
			<?php if (!in_array($sitelang, $content_languages) && in_array($sitelang, $home_pages)) : ?>
				<tr class="table-warning">
					<td>
						<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
						<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
					</td>
					<td>
						<?php echo Text::sprintf('COM_LANGUAGES_MULTILANGSTATUS_CONTENT_LANGUAGE_MISSING', $sitelang); ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	<table class="table table-sm">
		<thead>
			<tr>
				<th>
					<?php echo Text::_('JDETAILS'); ?>
				</th>
				<th class="text-center">
					<?php echo Text::_('JSTATUS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_LANGUAGEFILTER'); ?>
				</th>
				<td class="text-center">
					<?php if ($this->language_filter) : ?>
						<?php echo Text::_('JENABLED'); ?>
					<?php else : ?>
						<?php echo Text::_('JDISABLED'); ?>
					<?php endif; ?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_LANGSWITCHER_PUBLISHED'); ?>
				</th>
				<td class="text-center">
					<?php if ($this->switchers != 0) : ?>
						<?php echo $this->switchers; ?>
					<?php else : ?>
						<?php echo Text::_('JNONE'); ?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php if ($this->homes > 1) : ?>
						<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED_INCLUDING_ALL'); ?>
					<?php else : ?>
						<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED'); ?>
					<?php endif; ?>
				</th>
				<td class="text-center">
					<?php if ($this->homes > 1) : ?>
						<?php echo $this->homes; ?>
					<?php else : ?>
						<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED_ALL'); ?>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="table table-sm">
		<thead>
			<tr>
				<th>
					<?php echo Text::_('JGRID_HEADING_LANGUAGE'); ?>
				</th>
				<th class="text-center">
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_SITE_LANG_PUBLISHED'); ?>
				</th>
				<th class="text-center">
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_CONTENT_LANGUAGE_PUBLISHED'); ?>
				</th>
				<th class="text-center">
					<?php echo Text::_('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->statuses as $status) : ?>
				<?php if ($status->element) : ?>
					<tr>
						<td>
							<?php echo $status->element; ?>
						</td>
				<?php endif; ?>
				<?php // Published Site languages ?>
				<?php if ($status->element) : ?>
						<td class="text-center">
							<span class="fa fa-check" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
						</td>
				<?php else : ?>
						<td class="text-center">
							<?php echo Text::_('JNO'); ?>
						</td>
				<?php endif; ?>
				<?php // Published Content languages ?>
					<td class="text-center">
						<?php if ($status->lang_code && $status->published == 1) : ?>
							<span class="fa fa-check" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
						<?php elseif ($status->lang_code && $status->published == 0) : ?>
							<span class="fa fa-times" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JNO'); ?></span>
						<?php elseif ($status->lang_code && $status->published == -2) : ?>
							<span class="fa fa-trash" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
						<?php else : ?>
							<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
						<?php endif; ?>
					</td>
				<?php // Published Home pages ?>
				<?php if ($status->home_language) : ?>
						<td class="text-center">
							<span class="fa fa-check" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
						</td>
				<?php else : ?>
						<td class="text-center">
							<span class="fa fa-times" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JNO'); ?></span>
						</td>
				<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			<?php foreach ($this->contentlangs as $contentlang) : ?>
				<?php if (!array_key_exists($contentlang->lang_code, $this->site_langs)) : ?>
					<tr>
						<td>
							<?php echo $contentlang->lang_code; ?>
						</td>
						<td class="text-center">
							<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
						</td>
						<td class="text-center">
							<?php if ($contentlang->published == 1) : ?>
								<span class="fa fa-check" aria-hidden="true"></span>
								<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
							<?php elseif ($contentlang->published == 0 && array_key_exists($contentlang->lang_code, $this->homepages)) : ?>
								<span class="fa fa-times" aria-hidden="true"></span>
								<span class="sr-only"><?php echo Text::_('JNO'); ?></span>
							<?php elseif ($contentlang->published == -2 && array_key_exists($contentlang->lang_code, $this->homepages)) : ?>
								<span class="fa fa-trash" aria-hidden="true"></span>
								<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
							<?php endif; ?>
						</td>
						<td class="text-center">
							<?php if (!array_key_exists($contentlang->lang_code, $this->homepages)) : ?>
								<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
								<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
							<?php else : ?>
								<span class="fa fa-check" aria-hidden="true"></span>
								<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php // Display error when the Content Language has been deleted ?>
			<?php foreach ($sitelangs as $sitelang) : ?>
				<?php if (!in_array($sitelang, $content_languages) && in_array($sitelang, $home_pages)) : ?>
					<tr>
						<td>
							<?php echo $sitelang; ?>
						</td>
						<td class="text-center">
							<span class="fa fa-check" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
						</td>
						<td class="text-center">
							<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
						</td>
						<td class="text-center">
							<span class="fa fa-check" aria-hidden="true"></span>
							<span class="sr-only"><?php echo Text::_('JYES'); ?></span>
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
