#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup
import json

def get_license_links(page_url):
    """Fetch license links from a given page."""
    response = requests.get(page_url)
    if response.status_code != 200:
        print(f"Failed to retrieve {page_url}")
        return []

    soup = BeautifulSoup(response.content, 'html.parser')
    # Debug: Print the HTML content
    print(soup.prettify())

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

    # Debug: Print the found license data
    print("Found license data:", license_data)
    
    return license_data

def extract_license_details(license_url):
    """Extract additional details from the license detail page."""
    response = requests.get(license_url)
    if response.status_code != 200:
        print(f"Failed to retrieve {license_url}")
        return None

    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Extract category
    category_elem = soup.find('div', class_='post--metadata-group')
    category = category_elem.find('a', class_='term-item').text.strip() if category_elem else "N/A"
    
    # Extract license details
    details = {
        'title': soup.find('h1', class_='entry-title').text.strip() if soup.find('h1', class_='entry-title') else "N/A",
        'category': category,
        'version': soup.find('span', class_='license-version').text.replace('Version', '').strip() if soup.find('span', class_='license-version') else "N/A",
        'osi_submitted_date': None,
        'osi_submitted_link': None,
        'osi_submitter': None,
        'osi_approved_date': None,
        'osi_board_minutes_link': None,
        'spdx_detail_page': None,
        'steward': None,
        'steward_url': None,
        'osi_approved': False,
        'license_body': None
    }

    # Extract submission details
    if submitted_span := soup.find('span', class_='license-release'):
        if submitted_link := submitted_span.find('a'):
            details['osi_submitted_date'] = submitted_link.text.strip()
            details['osi_submitted_link'] = submitted_link['href']

    # Extract submitter
    if submitter_span := soup.find('span', class_='license-submitter'):
        details['osi_submitter'] = submitter_span.text.replace('Submitter:', '').strip()

    # Extract approval date
    if approved_span := soup.find('span', class_='license-approved'):
        details['osi_approved_date'] = approved_span.text.replace('Approved:', '').strip()

    # Extract board minutes link
    if minutes_span := soup.find('span', class_='license-board-minutes'):
        if minutes_link := minutes_span.find('a'):
            details['osi_board_minutes_link'] = minutes_link['href']

    # Extract SPDX identifier
    if spdx_span := soup.find('span', class_='license-spdx'):
        details['spdx_identifier'] = spdx_span.text.replace('SPDX short identifier:', '').strip()

    # Extract steward information
    if steward_span := soup.find('span', class_='license-steward'):
        if steward_link := steward_span.find('a', class_='term-item'):
            details['steward'] = steward_link.text.strip()

    # Extract steward URL
    if steward_url_span := soup.find('span', class_='license-steward-url'):
        if steward_url_link := steward_url_span.find('a'):
            details['steward_url'] = steward_url_link['href']

    # Check if OSI approved
    details['osi_approved'] = bool(soup.find('img', alt='Open Source Initiative Approved License'))

    # Extract license body
    if license_content := soup.find('div', class_='entry-content post--content license-content'):
        if license_div := license_content.find('div'):
            details['license_body'] = license_div.text.strip()

    return details

def main():
    base_url = "https://opensource.org/licenses"
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

    print("License data extracted and saved to licenses.json")

if __name__ == "__main__":
    main()
