# OSI License Backup Tool

A Python script that creates a local backup of Open Source Initiative (OSI) license information. The script generates a structured JSON file containing license details, submission information, and full license text.

## Requirements
 - beautifulsoup4
 - requests

Install dependencies with:
```
pip install -r requirements.txt
```

## Usage
Simply run the script:
```
python licenses.py
```
This will:
 - Download all license pages from opensource.org
 - Extract detailed information for each license
 - Save the results to licenses_original.json

## Output Format

The script generates a JSON file containing an array of license objects. Here's a simplified example showing the BSD 1-Clause License:

```
{
"link": "https://opensource.org/license/bsd-1-clause",
"slug": "bsd-1-clause",
"title": "1-clause BSD License",
"spdx": "BSD-1-Clause",
"category": "Other/Miscellaneous",
"version": "N/A",
"osi_approved": true,
"license_body": "Copyright (c) [Year][Name of Organization] [All rights reserved].\n
Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:\n
Redistributions of source code must retain the above copyright notice, this list
of conditions and the following disclaimer.\n
THIS SOFTWARE IS PROVIDED BY [Name of Organization] \"AS IS\"..."
}
```

## Data Fields
| Field | Description |
|-------|-------------|
| link | URL to the license page on opensource.org |
| slug | URL-friendly identifier for the license |
| title | Full name of the license |
| spdx | SPDX identifier for the license |
| category | OSI category classification |
| version | Version number of the license (if applicable) |
| osi_submitted_date | Date the license was submitted to OSI |
| osi_submitted_link | Link to the submission discussion |
| osi_submitter | Name of person who submitted the license |
| osi_approved_date | Date the license was approved by OSI |
| osi_board_minutes_link | Link to board meeting minutes for approval |
| spdx_detail_page | SPDX identifier used in detail pages |
| steward | Organization maintaining the license |
| steward_url | URL to the steward's license page |
| osi_approved | Boolean indicating if OSI approved |
| license_body | Full text of the license |

## License

### Code
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

### Data
The license data, of which Opensource.org is the author, is licensed under a [Creative Commons Attribution 4.0 International License](https://web.archive.org/web/20230202005829/https://creativecommons.org/licenses/by/4.0/).

Questions about the copyright of a license should be directed to the license steward.

## Notes
 - The script handles pagination automatically
 - It prints progress as it processes each page
 - Unicode characters in license text are preserved
 - Failed requests are logged but don't stop execution
