<?php

namespace App\Traits;

trait HasXpRewards
{
    /**
     * All XP rewards mapping
     */
    protected function getXpRewards()
    {
        return [
            // Book Actions
            'book_added' => 5,
            'book_pages_10' => 2,
            'book_completed' => 20,
            'book_rated' => 3,
            'review_written' => 10,
            'review_helpful' => 5,
            'book_bookmarked' => 2,
            
            // Quiz Actions
            'quiz_taken' => 5,
            'quiz_passed' => 25,
            'quiz_perfect' => 50,
            'first_quiz' => 15,
            
            // Certificate Actions
            'certificate_earned' => 50,
            'course_module' => 10,
            'course_completed' => 40,
            
            // Community Actions
            'group_joined' => 5,
            'message_sent' => 2,
            'group_created' => 20,
            'message_liked' => 3,
            
            // Marketplace Actions
            'listing_uploaded' => 15,
            'item_sold' => 30,
            'item_purchased' => 5,
            'seller_rated' => 3,
            
            // Referral Actions
            'referral_complete' => 50,
            'referred_signed_up' => 25,
            'referral_milestone_5' => 100,
            'referral_milestone_10' => 200,
            
            // Daily/Streak Actions
            'daily_login' => 5,
            'streak_7_days' => 50,
            'streak_30_days' => 200,
            'streak_100_days' => 500,
            
            // Document Actions
            'document_uploaded' => 5,
            'ai_document_question' => 1,
            'pdf_converted' => 3,
            'audio_converted' => 10,
            
            // AI Actions
            'ai_question' => 1,
            'new_chat_session' => 2,
            'ai_recommendation' => 1,
            
            // Profile Actions
            'profile_50_complete' => 10,
            'profile_100_complete' => 25,
            'social_link_added' => 2,
            'avatar_uploaded' => 5,
            'cover_uploaded' => 5,
        ];
    }
    
    /**
     * Reward user with XP for an action
     */
    public function rewardXp($action, $checkLimit = true)
    {
        $rewards = $this->getXpRewards();
        $points = $rewards[$action] ?? 0;
        
        if ($points <= 0) {
            return false;
        }
        
        // Check daily limits for certain actions
        if ($checkLimit && $this->hasDailyLimit($action)) {
            $todayCount = $this->getActionCountToday($action);
            if ($todayCount >= $this->getDailyLimit($action)) {
                return false;
            }
            $this->logXpAction($action);
        }
        
        return $this->addXp($points);
    }
    
    /**
     * Check if action has daily limit
     */
    protected function hasDailyLimit($action)
    {
        $limits = [
            'message_sent' => 10,
            'ai_question' => 30,
            'ai_document_question' => 20,
        ];
        
        return isset($limits[$action]);
    }
    
    /**
     * Get daily limit for action
     */
    protected function getDailyLimit($action)
    {
        $limits = [
            'message_sent' => 10,
            'ai_question' => 30,
            'ai_document_question' => 20,
        ];
        
        return $limits[$action] ?? 0;
    }
    
    /**
     * Log XP action to prevent abuse
     */
    protected function logXpAction($action)
    {
        // You can create an xp_logs table to track daily limits
        // For now, we'll use cache
        $key = "xp_{$this->id}_{$action}_" . now()->toDateString();
        $count = cache()->get($key, 0);
        cache()->put($key, $count + 1, now()->endOfDay());
    }
    
    /**
     * Get action count for today
     */
    protected function getActionCountToday($action)
    {
        $key = "xp_{$this->id}_{$action}_" . now()->toDateString();
        return cache()->get($key, 0);
    }
    
    /**
     * Bulk reward for milestones
     */
    public function checkReferralMilestones()
    {
        $completedCount = $this->getCompletedReferralsAttribute();
        
        if ($completedCount >= 10 && !$this->hasReceivedMilestone(10)) {
            $this->rewardXp('referral_milestone_10');
            $this->markMilestoneReceived(10);
        } elseif ($completedCount >= 5 && !$this->hasReceivedMilestone(5)) {
            $this->rewardXp('referral_milestone_5');
            $this->markMilestoneReceived(5);
        }
    }
    
    protected function hasReceivedMilestone($count)
    {
        $key = "milestone_referral_{$count}_{$this->id}";
        return cache()->get($key, false);
    }
    
    protected function markMilestoneReceived($count)
    {
        $key = "milestone_referral_{$count}_{$this->id}";
        cache()->put($key, true, now()->addYear());
    }
}