import json
import openai
from dotenv import load_dotenv
import time
import os
import logging

def get_structured_license_info(license_name: str, client: openai.AzureOpenAI) -> dict:
    """Get all license information in a single structured query."""
    
    # Load the example template
    with open("enhance_template.json", "r") as f:
        example_template = f.read()
    
    system_prompt = """
    You are a software licensing expert. Provide accurate, structured information about software licenses.
    Return only valid JSON without any additional text or explanation.
    Follow the exact format of the example provided, but for the requested license.
    """
    
    user_prompt = f"""
    Here's an example for the MIT License:
    {example_template}

    Now, provide the same structured information for the {license_name} license.
    Ensure all information is accurate and based on widely accepted facts.
    """

    response = client.chat.completions.create(
        model="gpt-4o",  # This should match your deployment name in Azure
        messages=[
            {"role": "system", "content": system_prompt},
            {"role": "user", "content": user_prompt}
        ],
        response_format={ "type": "json_object" }
    )
    
    try:
        return json.loads(response.choices[0].message.content)
    except Exception as e:
        logging.error(f"Error processing {license_name}: {str(e)}")
        return None

def main():
    # Set up logging
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s',
        filename='enhance.log'
    )
    
    # Load environment variables from .env file
    load_dotenv()
    
    # Load existing license data
    with open("licenses_original.json", "r") as f:
        licenses = json.load(f)
    
    # Initialize OpenAI client (replaces AzureOpenAI initialization)
    client = openai.AzureOpenAI(
        azure_endpoint=os.getenv("AZURE_OPENAI_ENDPOINT"),  # Should be like "https://{your-resource-name}.openai.azure.com"
        api_key=os.getenv("AZURE_OPENAI_KEY"),
        api_version="2024-02-15-preview"
    )
    
    # Load existing enhanced data if it exists
    output_file = "licenses.json"
    enhanced_licenses = {}
    if os.path.exists(output_file):
        with open(output_file, "r") as f:
            enhanced_licenses = json.load(f)
    
    # Enhance each license
    for license_data in licenses:
        license_title = license_data['title']
        
        # Skip if already processed
        if license_title in enhanced_licenses:
            logging.info(f"Skipping {license_title} - already processed")
            continue
            
        logging.info(f"Processing {license_title}...")
        
        try:
            # Get all information in one query
            enhancements = get_structured_license_info(license_title, client)
            
            if enhancements:
                enhanced_licenses[license_title] = {
                    **license_data,
                    "enhanced": {
                        **enhancements,
                        "last_updated": time.strftime("%Y-%m-%d")
                    }
                }
                
                # Save after each successful enhancement
                with open(output_file, "w") as f:
                    json.dump(enhanced_licenses, f, indent=2)
                logging.info(f"Successfully enhanced and saved {license_title}")
            
        except Exception as e:
            logging.error(f"Failed to process {license_title}: {str(e)}")
            continue
            
        time.sleep(1)

if __name__ == "__main__":
    main()
