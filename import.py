import requests
from bs4 import BeautifulSoup
import json

# Base URL of the OSI licenses page
base_url = "https://opensource.org/licenses"

def get_license_links(page_url):
    """Fetch license links from a given page."""
    response = requests.get(page_url)
    if response.status_code != 200:
        print(f"Failed to retrieve {page_url}")
        return []

    soup = BeautifulSoup(response.content, 'html.parser')
    # Select all rows in the license table
    license_rows = soup.select('tr')

    license_data = []
    for row in license_rows:
        # Find the license title cell
        title_cell = row.find('td', class_='license-table--title')
        spdx_cell = row.find('td', class_='license-table--spdx')
        category_cell = row.find('td', class_='license-table--category')

        if title_cell:
            # Extract the link, slug, and title
            link_tag = title_cell.find('a', href=True)
            if link_tag:
                link = link_tag['href']
                slug = link.split('/')[-1]
                title = link_tag.text.strip()

                # Extract SPDX identifier and category
                spdx = spdx_cell.text.strip() if spdx_cell else "N/A"
                category = category_cell.text.strip() if category_cell else "N/A"

                license_data.append({
                    'link': link,
                    'slug': slug,
                    'title': title,
                    'spdx': spdx,
                    'category': category
                })

    return license_data

def extract_license_details(license_url):
    """Extract additional details from the license detail page."""
    response = requests.get(license_url)
    if response.status_code != 200:
        print(f"Failed to retrieve {license_url}")
        return None

    soup = BeautifulSoup(response.content, 'html.parser')
    # Example: Extract additional fields from the detail page
    # Adjust the selectors based on the actual HTML structure of the detail page
    additional_info = {
        'example_field': soup.find('div', class_='example-class').text.strip() if soup.find('div', class_='example-class') else "N/A"
    }

    return additional_info

def main():
    all_license_data = []
    page_number = 0

    while True:
        page_url = f"{base_url}?page={page_number}"
        license_links = get_license_links(page_url)
        
        if not license_links:
            break  # Exit loop if no more licenses are found

        for license_info in license_links:
            # Use the full link directly from the dictionary
            full_license_url = license_info['link']
            additional_details = extract_license_details(full_license_url)
            if additional_details:
                license_info.update(additional_details)
                all_license_data.append(license_info)
                # Break after the first license for debugging
                break

        # Break after the first page for debugging
        break

    # Save all data to a JSON file
    with open('licenses.json', 'w') as json_file:
        json.dump(all_license_data, json_file, indent=4)

    print("First license data extracted and saved to first_license_debug.json")

if __name__ == "__main__":
    main()
