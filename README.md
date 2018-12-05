This is Gabriel Ong's course project for a PriCoSha.
PriCoSha: A application using a system for privately sharing content items among groups of people. PriCoSha gives users somewhat more privacy than many content sharing sites by giving them more detailed control over who can see which content items they post and more control over whether other people can tag content items with a user's personal information.

This is implemented in a webapp created using PHP, MySQL, HTML, and CSS. DB originally hosted on a Wamp 3.1.3 server.
Source code can be found in the "source" folder and is heavily commented with explanations, as well as citations to certain tutorials followed and some CSS used.
This implementation has a focus on text posts and a title to each post. Not currently supporting posting media content.

Use Cases:
1) View Public Content: The main page shows the user a feed of (item_id, email_post, post_time, file_path, and item_name) content items that are
visible to them from the last 24 hours, arranged in chronological order. There is also a tag button for each post that leads to a page with extended content information, including the names of people who are tagged and who they are tagged by.

2) Login: Login using existing credentials using a unique email and a hashed password. Initiates a session storing relevant session variables then goes to the home page. If the user/pass does not match or if they are empty, refuse login. The first page of the website is a registration page. Each page has a sign out button that returns you to the login page.

Features:
3) Tags: Users can tag either themselves or others in a post by submitting a form with the targeted user's email. If the user tags themselves, the tag is automatically approved. If not, the other user has to approve or decline or ignore the tag in their own Manage Tag page.

4) Add Friend: Users can add anyone to their existing FriendGroup in the Add Friend page, where their owned groups will be displayed. Users add by first and last name.

5) Create Friend Group: Users can create friend groups.

6) Users can Post with updates, notices, and other text content to everyone, or privately to their friendgroups.