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
 * @property int $id
 * @property int $user_id
 * @property string $user_message
 * @property string $ai_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereAiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereUserMessage($value)
 * @mixin \Eloquent
 */
	class AiChat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string|null $description
 * @property string|null $cover_image
 * @property string $file_path
 * @property bool $is_paid
 * @property numeric $price
 * @property int $total_pages
 * @property int $downloads
 * @property int $uploaded_by
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $embedding
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $allReviews
 * @property-read int|null $all_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificate> $certificates
 * @property-read int|null $certificates_count
 * @property-read mixed $bookmark_count
 * @property-read array $rating_distribution
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quiz> $quizzes
 * @property-read int|null $quizzes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User|null $uploader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereTotalPages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereUploadedBy($value)
 * @mixin \Eloquent
 */
	class Book extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $bookmarkable_type
 * @property int $bookmarkable_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $bookmarkable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereBookmarkableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereBookmarkableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereUserId($value)
 * @mixin \Eloquent
 */
	class Bookmark extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $certificate_number
 * @property string|null $file_path
 * @property int $quiz_score
 * @property int $total_questions
 * @property numeric $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCertificateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereQuizScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereTotalQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUserId($value)
 * @mixin \Eloquent
 */
	class Certificate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property array<array-key, mixed>|null $messages
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUserId($value)
 * @mixin \Eloquent
 */
	class ChatSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property string|null $file_path
 * @property string|null $file_type
 * @property int|null $file_size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUserId($value)
 * @mixin \Eloquent
 */
	class Document extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $cover_image
 * @property int $created_by
 * @property int $member_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessage> $messages
 * @property-read int|null $messages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereMemberCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUserId($value)
 * @mixin \Eloquent
 */
	class GroupMember extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereUserId($value)
 * @mixin \Eloquent
 */
	class GroupMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $seller_id
 * @property string $title
 * @property string $description
 * @property string $book_file
 * @property string|null $cover_image
 * @property numeric $price
 * @property string $status
 * @property string|null $admin_notes
 * @property int $views
 * @property int $downloads
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereBookFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereViews($value)
 * @mixin \Eloquent
 */
	class MarketplaceListing extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property array<array-key, mixed>|null $data
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 * @mixin \Eloquent
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $payable_type
 * @property int $payable_id
 * @property numeric $amount
 * @property string|null $method
 * @property string|null $transaction_id
 * @property string $status
 * @property string|null $reference
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $payable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUserId($value)
 * @mixin \Eloquent
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $quiz_id
 * @property string $question
 * @property string $option_a
 * @property string $option_b
 * @property string $option_c
 * @property string $option_d
 * @property string $correct_answer
 * @property int $points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Quiz|null $quiz
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptionA($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptionB($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptionC($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptionD($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $image
 * @property int $time_limit
 * @property int $passing_score
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuizAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read mixed $total_points
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz wherePassingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereTimeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Quiz extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $quiz_id
 * @property int $score
 * @property int $total_points
 * @property int $percentage
 * @property int $passed
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Quiz|null $quiz
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt wherePassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereTotalPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereUserId($value)
 * @mixin \Eloquent
 */
	class QuizAttempt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUserId($value)
 * @mixin \Eloquent
 */
	class Rating extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $referrer_id
 * @property int $referred_id
 * @property string $referral_code
 * @property numeric $referrer_earned
 * @property numeric $referred_earned
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $referred
 * @property-read \App\Models\User|null $referrer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferredEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferredId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferrerEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Referral extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $review
 * @property bool $is_approved
 * @property int $helpful_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereHelpfulCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 * @mixin \Eloquent
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property numeric $amount
 * @property numeric $balance_after
 * @property string|null $description
 * @property string|null $reference
 * @property string $status
 * @property string|null $method
 * @property string|null $payable_type
 * @property int|null $payable_id
 * @property int|null $book_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent|null $payable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUserId($value)
 * @mixin \Eloquent
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $google_id
 * @property string $role
 * @property float $wallet_balance
 * @property string|null $mpesa_phone
 * @property string|null $tigopesa_phone
 * @property string|null $halopesa_phone
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string|null $bank_account_name
 * @property string|null $avatar
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $bio
 * @property string|null $cover_photo
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property string|null $linkedin_url
 * @property string|null $github_url
 * @property string|null $instagram_url
 * @property string|null $website_url
 * @property string|null $location
 * @property string|null $occupation
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $referral_code
 * @property int|null $referred_by
 * @property numeric $referral_earnings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AiChat> $aiChats
 * @property-read int|null $ai_chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read int|null $books_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Certificate> $certificates
 * @property-read int|null $certificates_count
 * @property-read mixed $avatar_url
 * @property-read mixed $average_quiz_score
 * @property-read mixed $completed_referrals
 * @property-read mixed $cover_photo_url
 * @property-read mixed $joined_date
 * @property-read mixed $pending_referrals
 * @property-read mixed $referral_link
 * @property-read array $social_links
 * @property-read mixed $total_books_read
 * @property-read mixed $total_certificates
 * @property-read mixed $total_quizzes_passed
 * @property-read mixed $total_quizzes_taken
 * @property-read mixed $total_referrals
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketplaceListing> $marketplaceListings
 * @property-read int|null $marketplace_listings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $purchasedBooks
 * @property-read int|null $purchased_books_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuizAttempt> $quizAttempts
 * @property-read int|null $quiz_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Referral> $referrals
 * @property-read int|null $referrals_count
 * @property-read User|null $referredBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $referredUsers
 * @property-read int|null $referred_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCoverPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGithubUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHalopesaPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInstagramUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMpesaPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferralEarnings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTigopesaPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWalletBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWebsiteUrl($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $quiz_attempt_id
 * @property int $question_id
 * @property string $selected_answer
 * @property bool $is_correct
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Question|null $question
 * @property-read \App\Models\QuizAttempt|null $quizAttempt
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereQuizAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereSelectedAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class UserAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $progress_percent
 * @property int $current_page
 * @property string $status
 * @property string|null $purchased_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereCurrentPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereProgressPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook wherePurchasedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereUserId($value)
 * @mixin \Eloquent
 */
	class UserBook extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTopup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTopup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTopup query()
 * @mixin \Eloquent
 */
	class WalletTopup extends \Eloquent {}
}

