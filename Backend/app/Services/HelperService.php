<?php

namespace App\Services;


use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class HelperService
{
    // Helper function to compare detail (single)
    public static function areDetailEqual($modelDetail, $inputDetail)
    {
        // Convert modelDetail to array if needed
        if ($modelDetail instanceof Model) {
            $modelDetail = $modelDetail->toArray();
        } elseif ($modelDetail instanceof Collection) {
            $modelDetail = $modelDetail->toArray();
        }

        // Convert inputDetail to array if needed
        if ($inputDetail instanceof Model) {
            $inputDetail = $inputDetail->toArray();
        } elseif ($inputDetail instanceof Collection) {
            $inputDetail = $inputDetail->toArray();
        }

        // Remove all ended with _at
        $modelDetail = array_filter($modelDetail, function ($key) {
            return !str_ends_with($key, '_at');
        }, ARRAY_FILTER_USE_KEY);

        $inputDetail = array_filter($inputDetail, function ($key) {
            return !str_ends_with($key, '_at');
        }, ARRAY_FILTER_USE_KEY);

        // Enhanced ID mapping - handle various identification fields in input
        $identificationFields = ['detail_id', 'product_id', 'account_code'];

        foreach ($identificationFields as $field) {
            if (isset($inputDetail[$field]) && !isset($inputDetail['id']) && isset($modelDetail['id'])) {
                $inputDetail['id'] = $inputDetail[$field];
                break; // Only map the first matching identification field
            }
        }

        // Get the keys from input detail to know which fields to compare
        $inputKeys = array_keys($inputDetail);

        // Compare only fields that exist in the input
        foreach ($inputKeys as $key) {
            // Skip identification fields that were mapped to id
            if (in_array($key, $identificationFields) && isset($inputDetail['id']) && $inputDetail[$key] == $inputDetail['id']) {
                continue;
            }

            // Skip if the key doesn't exist in model
            if (!isset($modelDetail[$key])) {
                continue;
            }

            // Special handling for passengers array
            if ($key === 'passengers') {
                // If model has passengers relation loaded
                if (isset($modelDetail['passengers']) && is_array($modelDetail['passengers'])) {
                    // Compare passengers arrays
                    if (!self::arePassengersEqual($modelDetail['passengers'], $inputDetail['passengers'])) {

                        return false;
                    }
                }
                continue; // Skip further comparison for passengers
            }

            // Special handling for time fields (e.g., "08:00:00" vs "08:00")
            if (preg_match('/time$/', $key) && is_string($modelDetail[$key]) && is_string($inputDetail[$key])) {
                $modelTime = substr($modelDetail[$key], 0, 5); // Get first 5 chars (HH:MM)
                $inputTime = substr($inputDetail[$key], 0, 5); // Get first 5 chars (HH:MM)

                if ($modelTime === $inputTime) {
                    continue; // Times match when comparing only HH:MM part
                }
            }

            // If values don't match for this key
            if ($inputDetail[$key] != $modelDetail[$key]) {
                return false;
            }
        }

        return true;
    }

    // Helper function to compare details (many)
    public static function areDetailsEqual($modelDetails, $inputDetails)
    {
        // Convert single model to collection if needed
        if (!is_array($modelDetails) && !($modelDetails instanceof Collection)) {
            $modelDetails = collect([$modelDetails]);
        }

        // Convert single input to array if needed
        if (!is_array($inputDetails) && !($inputDetails instanceof Collection)) {
            $inputDetails = [$inputDetails];
        }

        // First check if counts are different
        if (count($modelDetails) !== count($inputDetails)) {
            return false;
        }

        // Convert to arrays for consistent comparison
        $modelDetailsArray = $modelDetails instanceof Model
            ? [$modelDetails->toArray()]
            : ($modelDetails instanceof Collection
                ? $modelDetails->toArray()
                : $modelDetails);

        // Create associative arrays with ID as key for easier comparison
        $modelDetailsById = collect($modelDetailsArray)->keyBy('id')->toArray();

        // Map input details to use same key structure as model
        $mappedInputDetails = [];
        foreach ($inputDetails as $input) {
            // Enhanced identification field mapping
            $identificationFields = ['detail_id', 'product_id', 'account_code'];
            $id = null;

            // Try to find an identification field that exists in the input
            foreach ($identificationFields as $field) {
                if (isset($input[$field])) {
                    $id = $input[$field];
                    break;
                }
            }

            // Fallback to 'id' if none of the identification fields were found
            if ($id === null) {
                $id = $input['id'] ?? null;
            }

            if ($id !== null) {
                $mappedInput = $input;

                // Set the id field to ensure consistency
                if (!isset($mappedInput['id'])) {
                    $mappedInput['id'] = $id;
                }

                // Handle time fields in each detail
                foreach ($mappedInput as $key => $value) {
                    if (preg_match('/time$/', $key) && is_string($value) && strlen($value) <= 5) {
                        // If time format is HH:MM, pad it to match HH:MM:00
                        $mappedInput[$key] = $value . (strlen($value) === 5 ? ':00' : '');
                    }
                }

                $mappedInputDetails[$id] = $mappedInput;
            }
        }

        // Check if same IDs exist in both arrays
        $modelIds = array_keys($modelDetailsById);
        $inputIds = array_keys($mappedInputDetails);

        // Sort the IDs arrays to ensure order doesn't matter
        sort($modelIds);
        sort($inputIds);

        if ($modelIds != $inputIds) {
            // IDs don't match
            $modelOnly = array_diff($modelIds, $inputIds);
            $inputOnly = array_diff($inputIds, $modelIds);

            return false;
        }

        // Compare each detail using the single detail comparison method
        foreach ($mappedInputDetails as $id => $inputDetail) {
            $modelDetail = $modelDetailsById[$id];

            if (!self::areDetailEqual($modelDetail, $inputDetail)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compare model and input details
     *
     * @param mixed $modelDetails Model details (single or collection)
     * @param mixed $inputDetails Input details (single or array)
     * @param string $context Optional context string to identify this comparison
     * @return bool Result of comparison
     */
    public static function debugCompare($modelDetails, $inputDetails, $context = 'comparison')
    {
        $result = self::areDetailsEqual($modelDetails, $inputDetails);

        return $result;
    }

    /**
     * Compare passengers arrays to check if they are equal
     *
     * @param array $modelPassengers Passenger array from model
     * @param array $inputPassengers Passenger array from input
     * @return bool Result of comparison
     */
    public static function arePassengersEqual($modelPassengers, $inputPassengers)
    {
        // First check if counts are different
        if (count($modelPassengers) !== count($inputPassengers)) {

            return false;
        }

        // Create associative arrays for easier comparison
        $modelPassengersById = [];
        foreach ($modelPassengers as $passenger) {
            $id = $passenger['id'] ?? null;
            if ($id !== null) {
                $modelPassengersById[$id] = $passenger;
            }
        }

        // Try to match input passengers to model passengers
        $matchedInputs = [];

        // First try to match by ID
        foreach ($inputPassengers as $inputPassenger) {
            $id = $inputPassenger['id'] ?? null;

            if ($id !== null && isset($modelPassengersById[$id])) {
                // Found a match by ID
                $modelPassenger = $modelPassengersById[$id];

                // Compare important fields
                if (
                    ($inputPassenger['name'] ?? '') != ($modelPassenger['name'] ?? '') ||
                    ($inputPassenger['phone_number_1'] ?? '') != ($modelPassenger['phone_number_1'] ?? '') ||
                    ($inputPassenger['pickup_address'] ?? '') != ($modelPassenger['pickup_address'] ?? '') ||
                    ($inputPassenger['destination_address'] ?? '') != ($modelPassenger['destination_address'] ?? '') ||
                    ($inputPassenger['price'] ?? 0) != ($modelPassenger['price'] ?? 0) ||
                    ($inputPassenger['payment_method_id'] ?? null) != ($modelPassenger['payment_method_id'] ?? null)
                ) {
                    // Fields don't match for this passenger

                    return false;
                }

                $matchedInputs[] = $id;
            }
        }

        // If we couldn't match all passengers by ID, try matching by customer_id or other fields
        if (count($matchedInputs) !== count($inputPassengers)) {
            // Filter out already matched inputs
            $unmatchedInputs = array_filter($inputPassengers, function ($p) use ($matchedInputs) {
                return !isset($p['id']) || !in_array($p['id'], $matchedInputs);
            });

            // Try matching by customer_id next
            foreach ($unmatchedInputs as $inputPassenger) {
                $found = false;
                $customerID = $inputPassenger['customer_id'] ?? null;

                if ($customerID !== null) {
                    foreach ($modelPassengers as $modelPassenger) {
                        if (($modelPassenger['customer_id'] ?? null) == $customerID) {
                            $found = true;
                            break;
                        }
                    }
                }

                if (!$found) {
                    // Try matching by phone and name as last resort
                    $phone = $inputPassenger['phone_number_1'] ?? '';
                    $name = $inputPassenger['name'] ?? '';

                    if ($phone && $name) {
                        foreach ($modelPassengers as $modelPassenger) {
                            if (
                                ($modelPassenger['phone_number_1'] ?? '') == $phone &&
                                ($modelPassenger['name'] ?? '') == $name
                            ) {
                                $found = true;
                                break;
                            }
                        }
                    }
                }

                // If we couldn't find a match for this passenger by any method
                if (!$found) {

                    return false;
                }
            }
        }

        return true;
    }
}
