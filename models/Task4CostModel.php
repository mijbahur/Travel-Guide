<?php
// Task 4 Cost Model - Handle cost estimates for travel posts
require_once __DIR__ . '/../config/db.php';

/**
 * Get base cost for a post from database or use fallback mapping
 * @param int $postId The post ID
 * @param string $costLevel The cost level (low, medium, high)
 * @return array Array with 'base_cost' and 'currency' keys
 */
function getTask4BaseCost($postId, $costLevel)
{
    $postId = (int)$postId;
    $db = getConnection();
    
    // Try to get from cost_estimates table
    $sql = "SELECT base_cost, currency FROM cost_estimates WHERE post_id = :post_id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([':post_id' => $postId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return $result;
    }
    
    // Fallback mapping based on cost level
    $fallbackCosts = [
        'low' => 500,
        'medium' => 1500,
        'high' => 3000
    ];
    
    $baseCost = $fallbackCosts[$costLevel] ?? 1500; // Default to medium if invalid
    
    return [
        'base_cost' => $baseCost,
        'currency' => 'USD'
    ];
}

/**
 * Calculate total cost based on base cost, number of travelers, and duration
 * Formula: total = baseCost * travelers * days / 7
 * @param float $baseCost Base cost amount
 * @param int $travelers Number of travelers
 * @param int $days Number of days
 * @return float Total cost rounded to 2 decimal places
 */
function calculateTask4TotalCost($baseCost, $travelers, $days)
{
    $baseCost = (float)$baseCost;
    $travelers = (int)$travelers;
    $days = (int)$days;
    
    $total = ($baseCost * $travelers * $days) / 7;
    return round($total, 2);
}
