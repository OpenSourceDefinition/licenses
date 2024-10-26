import json
import openai
from dotenv import load_dotenv
import time
import os

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
    
    return json.loads(response.choices[0].message.content)

def main():
    # Load environment variables from .env file
    load_dotenv()
    
    # Load existing license data
    with open("licenses.json", "r") as f:
        licenses = json.load(f)
    
    # Initialize OpenAI client (replaces AzureOpenAI initialization)
    client = openai.AzureOpenAI(
        azure_endpoint=os.getenv("AZURE_OPENAI_ENDPOINT"),  # Should be like "https://{your-resource-name}.openai.azure.com"
        api_key=os.getenv("AZURE_OPENAI_KEY"),
        api_version="2024-02-15-preview"
    )
    
    enhanced_licenses = {}
    
    # Enhance each license
    for license_data in licenses:
        license_title = license_data['title']
        print(f"Enhancing {license_title}...")
        
        # Get all information in one query
        enhancements = get_structured_license_info(license_title, client)
        
        # Keep original data separate from AI enhancements
        enhanced_licenses[license_title] = {
            **license_data,
            "enhanced": {  # New nested structure for AI-generated content
                **enhancements,
                "last_updated": time.strftime("%Y-%m-%d")
            }
        }
        
        # break # only do one license for debugging

        # Add rate limiting delay
        time.sleep(1)
    
    # Save enhanced data
    with open("licenses_enhanced.json", "w") as f:
        json.dump(enhanced_licenses, f, indent=2)

if __name__ == "__main__":
    main()
