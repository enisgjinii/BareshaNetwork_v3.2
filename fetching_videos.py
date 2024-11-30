from googleapiclient.discovery import build
import pandas as pd

# Replace with your YouTube Data API Key
API_KEY = 'AIzaSyCRFtIfiEyeYmCrCZ8Bvy8Z4IPBy1v2iwo'
# Replace with the channel's ID
CHANNEL_ID = 'UCUr1guLUtjwgJCd9gEQ0HRw'

def fetch_videos(api_key, channel_id):
    # Initialize YouTube API client
    youtube = build('youtube', 'v3', developerKey=api_key)
    
    # Step 1: Get the uploads playlist ID
    channel_response = youtube.channels().list(
        part='contentDetails',
        id=channel_id
    ).execute()
    
    uploads_playlist_id = channel_response['items'][0]['contentDetails']['relatedPlaylists']['uploads']
    
    # Step 2: Fetch all videos from the playlist
    videos = []
    next_page_token = None

    while True:
        playlist_response = youtube.playlistItems().list(
            part='snippet',
            playlistId=uploads_playlist_id,
            maxResults=50,
            pageToken=next_page_token
        ).execute()

        for item in playlist_response['items']:
            video_id = item['snippet']['resourceId']['videoId']
            video_title = item['snippet']['title']
            video_published_date = item['snippet']['publishedAt']
            video_link = f"https://www.youtube.com/watch?v={video_id}"
            videos.append({
                'Video ID': video_id,
                'Video Link': video_link,
                'Video Title': video_title,
                'Video Published Date': video_published_date
            })

        next_page_token = playlist_response.get('nextPageToken')
        if not next_page_token:
            break
    
    # Step 3: Sort videos by published date (oldest to newest)
    videos = sorted(videos, key=lambda x: x['Video Published Date'])
    
    return videos

# Fetch videos
videos = fetch_videos(API_KEY, CHANNEL_ID)

# Save the results to a CSV file
df = pd.DataFrame(videos)
df.to_csv('youtube_videos_oldest_to_newest.csv', index=False)

# Display results
print(df)
