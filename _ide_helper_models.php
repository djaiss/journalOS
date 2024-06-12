<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\JournalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Journal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Journal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Journal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereUserId($value)
 */
	class Journal extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $journal_id
 * @property string|null $title
 * @property bool $is_published
 * @property int $number_of_words
 * @property int $reading_time_in_seconds
 * @property \Illuminate\Support\Carbon $written_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $excerpt
 * @property-read \App\Models\Journal $journal
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostSection> $postSections
 * @property-read int|null $post_sections_count
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereJournalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereNumberOfWords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereReadingTimeInSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereWrittenAt($value)
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $post_id
 * @property int $position
 * @property string $label
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Post $post
 * @method static \Database\Factories\PostSectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostSection whereUpdatedAt($value)
 */
	class PostSection extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property-read string|null $label
 * @property string|null $label_translation_key
 * @property int $position
 * @property bool $can_be_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostTemplateSection> $postTemplateSections
 * @property-read int|null $post_template_sections_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PostTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereCanBeDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereLabelTranslationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplate whereUserId($value)
 */
	class PostTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $post_template_id
 * @property-read string|null $label
 * @property string|null $label_translation_key
 * @property int $position
 * @property bool $can_be_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PostTemplate $postTemplate
 * @method static \Database\Factories\PostTemplateSectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection whereCanBeDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection whereLabelTranslationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection wherePostTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostTemplateSection whereUpdatedAt($value)
 */
	class PostTemplateSection extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $nickname
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Journal> $journals
 * @property-read int|null $journals_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostTemplate> $postTemplates
 * @property-read int|null $post_templates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

