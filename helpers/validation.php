<?php
/**
 * ============================================================================
 * INPUT VALIDATION HELPERS & FORM RULES
 * ============================================================================
 */

require_once __DIR__ . '/response.php';

/**
 * Validate Session ID format
 *
 * @param string $sessionId
 * @return bool
 */
function isValidSessionId($sessionId) {
    if (empty($sessionId) || !is_string($sessionId)) {
        return false;
    }
    // Accept SM-SESSION-XXXXXXXX, AT-SESSION-XXXXXXXX, or safe alphanumeric 10-64 chars
    return (bool)preg_match('/^[A-Za-z0-9_-]{10,64}$/', $sessionId);
}

/**
 * Validate and clean Indian 10-digit mobile number
 *
 * @param string $mobile
 * @return string|false Normalized 10-digit number or false if invalid
 */
function validateIndianMobile($mobile) {
    if (empty($mobile) || !is_string($mobile)) {
        return false;
    }

    // Strip spaces, dashes, parentheses
    $clean = preg_replace('/[\s\-\(\)]/', '', trim($mobile));

    // Strip leading +91 or 91 if present
    if (strpos($clean, '+91') === 0) {
        $clean = substr($clean, 3);
    } elseif (strpos($clean, '91') === 0 && strlen($clean) === 12) {
        $clean = substr($clean, 2);
    } elseif (strpos($clean, '0') === 0 && strlen($clean) === 11) {
        $clean = substr($clean, 1);
    }

    // Must be exactly 10 digits starting with 6, 7, 8, or 9
    if (preg_match('/^[6-9]\d{9}$/', $clean)) {
        return $clean;
    }

    return false;
}

/**
 * Validate Person's Full Name
 *
 * @param string $name
 * @return string|false Sanitized name or false
 */
function validateFullName($name) {
    $clean = cleanString($name);
    if (strlen($clean) < 2 || strlen($clean) > 60) {
        return false;
    }
    if (!preg_match("/^[A-Za-z\s\.\-']{2,60}$/", $clean)) {
        return false;
    }
    return $clean;
}

/**
 * Valid Questions & Allowed Options Map
 */
function getQuestionsMap() {
    return [
        1 => [
            'question' => "What’s your biggest challenge right now?",
            'field' => 'challenge',
            'allowed_options' => [
                'Frequent stop-loss hits',
                'Wrong position sizing',
                'No proper exit plan',
                'Emotional decision-making'
            ]
        ],
        2 => [
            'question' => "Your Contact Details",
            'fields' => ['full_name', 'mobile_number']
        ],
        3 => [
            'question' => "Which segment do you trade in?",
            'field' => 'trading_segment',
            'allowed_options' => [
                'Equity',
                'Options',
                'Futures',
                'Commodity'
            ]
        ],
        4 => [
            'question' => "Trading capital?",
            'field' => 'trading_capital',
            'allowed_options' => [
                'Below 50K',
                '50K–1 Lakh',
                '1 Lakh–2 Lakh',
                '2 Lakh+'
            ]
        ],
        5 => [
            'question' => "Final Details",
            'fields' => ['whatsapp_number', 'state']
        ]
    ];
}

/**
 * List of valid Indian States & Union Territories
 */
function getValidStates() {
    return [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
        'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
        'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
        'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
        'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
        'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
        'Andaman and Nicobar Islands', 'Chandigarh',
        'Dadra and Nagar Haveli and Daman and Diu', 'Delhi (NCT)',
        'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
    ];
}

/**
 * Validate step payload for track-step endpoint
 *
 * @param int $step
 * @param array $data
 * @return array ['valid' => bool, 'error' => string|null, 'sanitized' => array]
 */
function validateStepData($step, array $data) {
    $sanitized = [];

    switch ($step) {
        case 1:
            $challenge = cleanString($data['challenge'] ?? '');
            $allowed = getQuestionsMap()[1]['allowed_options'];
            if (!in_array($challenge, $allowed, true)) {
                return ['valid' => false, 'error' => 'Please select a valid challenge option.'];
            }
            $sanitized['challenge'] = $challenge;
            break;

        case 2:
            $fullName = validateFullName($data['full_name'] ?? ($data['fullName'] ?? ''));
            if (!$fullName) {
                return ['valid' => false, 'error' => 'Please provide a valid full name (letters only, min 2 characters).'];
            }
            $mobile = validateIndianMobile($data['mobile_number'] ?? ($data['mobileNumber'] ?? ''));
            if (!$mobile) {
                return ['valid' => false, 'error' => 'Please provide a valid 10-digit Indian mobile number.'];
            }
            $sanitized['full_name'] = $fullName;
            $sanitized['mobile_number'] = $mobile;
            break;

        case 3:
            $segment = cleanString($data['trading_segment'] ?? ($data['tradingSegment'] ?? ''));
            $allowed = getQuestionsMap()[3]['allowed_options'];
            if (!in_array($segment, $allowed, true)) {
                return ['valid' => false, 'error' => 'Please select a valid trading segment.'];
            }
            $sanitized['trading_segment'] = $segment;
            break;

        case 4:
            $capital = cleanString($data['trading_capital'] ?? ($data['tradingCapital'] ?? ''));
            $allowed = getQuestionsMap()[4]['allowed_options'];
            if (!in_array($capital, $allowed, true)) {
                return ['valid' => false, 'error' => 'Please select a valid trading capital bracket.'];
            }
            $sanitized['trading_capital'] = $capital;
            break;

        case 5:
            $wa = validateIndianMobile($data['whatsapp_number'] ?? ($data['whatsappNumber'] ?? ''));
            if (!$wa) {
                return ['valid' => false, 'error' => 'Please provide a valid 10-digit WhatsApp number.'];
            }
            $state = cleanString($data['state'] ?? ($data['userState'] ?? ''));
            $validStates = getValidStates();
            if (!in_array($state, $validStates, true)) {
                return ['valid' => false, 'error' => 'Please select a valid state from the list.'];
            }
            $sanitized['whatsapp_number'] = $wa;
            $sanitized['state'] = $state;
            break;

        default:
            return ['valid' => false, 'error' => 'Invalid step number.'];
    }

    return ['valid' => true, 'error' => null, 'sanitized' => $sanitized];
}
