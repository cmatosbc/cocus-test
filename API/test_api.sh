#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

API_URL="http://localhost:5000/python_api"

print_separator() {
    echo -e "\n${YELLOW}----------------------------------------${NC}"
}

test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local expected_status=$4
    
    echo -e "\n${YELLOW}Testing ${method} ${endpoint}${NC}"
    
    if [ -n "$data" ]; then
        response=$(curl -s -w "\n%{http_code}" -X ${method} "${API_URL}${endpoint}" \
            -H "Content-Type: application/json" \
            -d "${data}")
    else
        response=$(curl -s -w "\n%{http_code}" -X ${method} "${API_URL}${endpoint}")
    fi
    
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')
    
    if [ "$status_code" -eq "$expected_status" ]; then
        echo -e "${GREEN}✓ Status: ${status_code}${NC}"
        if [ -n "$body" ]; then
            echo "Response:"
            echo "$body" | python3 -m json.tool
        fi
        return 0
    else
        echo -e "${RED}✗ Expected status ${expected_status}, got ${status_code}${NC}"
        if [ -n "$body" ]; then
            echo "Error response:"
            echo "$body" | python3 -m json.tool
        fi
        return 1
    fi
}

echo "Starting Python API Tests"
print_separator

# Test 1: GET all notes
test_endpoint "GET" "/notes" "" 200

# Test 2: Create a note
print_separator
note_data='{
    "title": "Test Note",
    "message": "This is a test note",
    "type": 0,
    "user_id": 1
}'
test_endpoint "POST" "/notes" "$note_data" 201

if [ $? -eq 0 ]; then
    # Extract note ID from response
    note_id=$(echo "$response" | sed '$d' | python3 -c "import sys, json; print(json.load(sys.stdin).get('id', ''))")
    
    if [ -n "$note_id" ]; then
        # Test 3: Get single note
        print_separator
        test_endpoint "GET" "/notes/${note_id}" "" 200
        
        # Test 4: Update note
        print_separator
        update_data='{
            "title": "Updated Test Note",
            "message": "This note has been updated",
            "type": 1,
            "user_id": 1
        }'
        test_endpoint "PUT" "/notes/${note_id}" "$update_data" 200
        
        # Test 5: Delete note
        print_separator
        test_endpoint "DELETE" "/notes/${note_id}" "" 204
        
        # Test 6: Verify deletion
        print_separator
        echo -e "\n${YELLOW}Verifying deletion - should return 404${NC}"
        test_endpoint "GET" "/notes/${note_id}" "" 404
    else
        echo -e "${RED}✗ Could not extract note ID from response${NC}"
    fi
else
    echo -e "${RED}✗ Failed to create test note, skipping remaining tests${NC}"
fi

print_separator
echo -e "${GREEN}API Testing completed!${NC}"
