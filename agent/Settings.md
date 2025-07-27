# ElevenLabs Agent Settings

This file is an attempt to document which settings we're using. At this time, these values are not automatically synced or included in the agent build in any way. 

## Agent Settings

| Setting | Value |
| ------- | ----- |
| Agent Name | `Party Line Patrick` |
| Agent Language (Default) | `English` |
| Additional Languages | |
| First Message | [dist/Opener.txt](dist/Opener.txt) |
| First Message Language | `English` |
| System Prompt | [dist/SystemPrompt.txt](dist/SystemPrompt.txt) |
| System Prompt Timezone | `America/Los_Angeles` |
| System Prompt Variable | `system__time` |
| LLM | `Gemini 2.5 Flash` |
| Temperature | `Deterministic` (`0.00`) | 
| Limit token usage | `500` |
| Agent Knowledge Base | `Built into System Prompt` |
| Tools | `End call`, `Detect language`, `Skip Turn` |

## Voice Settings

| Setting | Value                                                                  |
| ------- |------------------------------------------------------------------------|
| Voice | `Patrick Labbett`                                                      |
| Multi-voice support | &mdash;                                                                |
| Use Flash | `Enabled`                                                              |
| TTS output format | `u-law 8000 Hz`                                                        |
| Pronunciation Dictionaries | [dist/pronunciation-dictionary.xml](dist/pronunciation-dictionary.xml) |
| Optimize streaming latency | `3`                                                                    |
| Stability | `0.60`                                                                 |
| Speed  | `1.02`                                                                 |
| Similarity | `0.60`                                                                 |

## Analysis Settings

| Setting  | Value |
| ------- | ----- |
| Evaluation criteria | &mdash; |
| Data collection | &mdash; |

> We are not using either of these features

## Security Settings

| Setting | Value |
| ------- | ----- |
| Enable authentication | &mdash; |
| Allowlist | `www.callcentervillage.com` |
| Enable overrides | &mdash; |
| Fetch initiation client data from webhook | &mdash; |
| Post-Call Webhook | &mdash; |
| Enable bursting | `Enabled` |
| Concurrent Calls Limit | `-1` |
| Daily Calls Limit | `1000` |


## Advanced Settings

| Setting | Value                                                                                                            |
| ------- |------------------------------------------------------------------------------------------------------------------|
| Turn timeout | `6`                                                                                                             |
| Silence end call timeout | `10`                                                                                                             |
| Max conversation duration | `150`                                                                                                            |
| Keywords | [dist/Keywords.txt](dist/Keywords.txt)                                                                           |
| Text only | `Disabled`                                                                                                       |
| User input audio format | `u-law 8000 Hz`                                                                                                  |
| Client Events | `audio`, `interruption`, `user_transcript`, `agent_response`, `agent_response_correction`, `agent_tool_response` |
| Store Call Audio | `Disabled`                                                                                                       |
| Zero-PII Retention Mode | `Enabled`                                                                                                        |
| Conversations Retention Period | `-1`                                                                                                             |


## Widget Settings

> We are not using the web widget.