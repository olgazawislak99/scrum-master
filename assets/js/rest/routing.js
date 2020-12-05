var client = new $.RestClient('/rest/api/');
client.add('user');
client.add('sprint');
client.add('goal');
client.add('usersGoals');
client.add('goalDesc');
client.add('project');

client.user.add('password');
export { client };