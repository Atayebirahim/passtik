#!/bin/bash

# Automated Localization Script for Passtik
# This script replaces common English text with translation keys

cd /opt/lampp/htdocs/passtik/resources/views

# Function to replace text in files
localize_file() {
    local file=$1
    
    # Skip already localized files
    if grep -q "__('messages\." "$file" 2>/dev/null; then
        echo "✓ $file already localized"
        return
    fi
    
    # Common replacements
    sed -i "s/'Passtik'/__('messages.app_name')/g" "$file" 2>/dev/null
    sed -i 's/>Passtik</>{!! __("messages.app_name") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Dashboard</>{!! __("messages.dashboard") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Routers</>{!! __("messages.routers") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Vouchers</>{!! __("messages.vouchers") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Users</>{!! __("messages.users") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Settings</>{!! __("messages.settings") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Status</>{!! __("messages.status") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Actions</>{!! __("messages.actions") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Active</>{!! __("messages.active") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Pending</>{!! __("messages.pending") !!}</g' "$file" 2>/dev/null
    sed -i 's/>View</>{!! __("messages.view") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Edit</>{!! __("messages.edit") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Delete</>{!! __("messages.delete") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Create</>{!! __("messages.create") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Save</>{!! __("messages.save") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Cancel</>{!! __("messages.cancel") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Back</>{!! __("messages.back") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Search</>{!! __("messages.search") !!}</g' "$file" 2>/dev/null
    sed -i 's/>Filter</>{!! __("messages.filter") !!}</g' "$file" 2>/dev/null
    
    echo "✓ Localized $file"
}

# Process all blade files
find . -name "*.blade.php" -type f | while read file; do
    localize_file "$file"
done

echo "Localization complete!"
