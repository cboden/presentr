# Prezentr (alpha)

The new, lively, hip(ster) HTML5 slideshow build with AngularJS and Ratchet.

This is an interactive slideshow tool for a presentation. It is built using AngularJS and Ratchet. 
Edit `vagrant/bootstrap.sh` to set the IP of your VirtualBox to something on your network (it'll be bridged) then `vagrant up`. 
The audience can connect, follow along, and interact with eachother on specified slides. 
The presenter can control which slide the audience is on when connected to /remote.html.

---

This was something I threw together for a presentation to the Guelph PHP User Group about React. 
The idea was to show off some capabilites of React and Ratchet while doing a traditional slideshow+speech. 
At the same time it was an excuse for me to learn some AngularJS. Everything went better than expected. 

Given the ease of use, the interactivity with the audience, and the feedback I'd like to abstract and enhance it. 
ToDo's are listed below. Issues will be enabled later, when I feel the project is in better shape and easier to use. 

### TODOs

* Separate React presentation from application
* Big code cleanup
* Fix slide offset in FireFox
* Test on IE (lol)
* notes directive, notes included in slide, only show when appropriate url
* pub/sub is completely messed up because controllers are always loaded - need to be active when slide is
* better authentication method for remote control
* ask questions through client to speaker

### Angular Slideshow initially from
 * https://github.com/IgorMinar/ng-slides
 * https://github.com/werk85/ng-slides
