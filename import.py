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
    """Extract details from a license page."""
    response = requests.get(license_url)
    if response.status_code != 200:
        print(f"Failed to retrieve {license_url}")
        return None

    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Extract details (adjust selectors based on actual HTML structure)
    license_name = soup.find('h1').text.strip()
    approval_date = soup.find('span', class_='approval-date').text.strip() if soup.find('span', class_='approval-date') else "N/A"
    spdx_identifier = soup.find('span', class_='spdx-identifier').text.strip() if soup.find('span', class_='spdx-identifier') else "N/A"
    board_minutes_link = soup.find('a', text='Board Minutes')['href'] if soup.find('a', text='Board Minutes') else "N/A"
    version = soup.find('span', class_='version').text.strip() if soup.find('span', class_='version') else "N/A"
    submitted_date = soup.find('span', class_='submitted-date').text.strip() if soup.find('span', class_='submitted-date') else "N/A"
    submitter = soup.find('span', class_='submitter').text.strip() if soup.find('span', class_='submitter') else "N/A"
    license_text = soup.find('div', class_='license-text').text.strip() if soup.find('div', class_='license-text') else "N/A"
    
    return {
        'name': license_name,
        'url': license_url,
        'approval_date': approval_date,
        'spdx_identifier': spdx_identifier,
        'board_minutes_link': board_minutes_link,
        'version': version,
        'submitted_date': submitted_date,
        'submitter': submitter,
        'license_text': license_text
    }

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
            license_data = extract_license_details(full_license_url)
            if license_data:
                all_license_data.append(license_data)
                # Break after the first license for debugging
                break

        # Break after the first page for debugging
        break

    # Save all data to a JSON file
    with open('licenses.json', 'w') as json_file:
        json.dump(all_license_data, json_file, indent=4)

    print("License/s extracted and saved to licenses.json")

if __name__ == "__main__":
    main()
