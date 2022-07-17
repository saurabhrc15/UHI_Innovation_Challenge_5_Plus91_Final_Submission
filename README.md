# UHI_Innovation_Challenge_5_Plus91_Final_Submission
 Optimising prescription and diagnostic generation through speech recognising
 
## Basic Architecture

## Command Supported

1. Prescription:
    - search medicine {medicine_name} (e.g search medicine aspirin) - Searches & shows list of medicines
    - select {ordinal_number} medicine (e.g select second medicine) - Selects medicine from select list.  Note: Currently only supports numbers first to tenth.
    - select medicine number {number} (e.g select medicine number 5) - Selects medicine from select list.
    - add another medicine - Creates space to add another medicine

2. ICD 10:
    - search icd10 code {code_name OR description} (e.g search icd10 code tuberculosis) - Searches & shows list of icd10 codes on modal.
    - select icd10 code number {number} (e.g select icd10 code number five) - Selects icd10 from list.

## Response Rendered from Speech API

```
{
    "SessionCode": "0d4a133a-f89e-11ea-bb1e-0a58646cd702",
    "Status": 0,
    "Event": "None",
    "Result": {
        "Final": true,
        "Transcript": " select icd10 code {code_name}",
        "IsCommand": true,
        "CommandAction": "select icd10 code {code_name}"
    }
}
```

## Reference

https://user-images.githubusercontent.com/97583085/179389539-0d84a796-085c-4cb8-a06b-b868f8c0530f.mp4





