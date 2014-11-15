<?php

class Utils_QuoteGenerator {
    
    private static $_quotes = array(

        array("Sarcasm: the last refuge of modest and chaste-souled people when the privacy of their soul is coarsely and intrusively invaded.", "Fyodor Dostoevsky"),

        array("Civilization is the progress toward a society of privacy. The savage's whole existence is public, ruled by the laws of his tribe. Civilization is the process of setting man free from men.", "Ayn Rand"),

        array("The government must give proper weight to both keeping America safe from terrorists and protecting Americans' privacy. But when Americans lack the most basic information about our domestic surveillance programs, they have no way of knowing whether we're getting that balance right. This lack of transparency is a big problem.", "Al Franken"),

        array("In digital era, privacy must be a priority. Is it just me, or is secret blanket surveillance obscenely outrageous?", "Al Gore"),

        array("I think privacy is valuable. You don't have to share everything, and it's healthy to occasionally hit the pause button and ask yourself if you're oversharing. But at the end of the day, if you're not doing anything wrong, you don't have anything to hide.", "Ashton Kutcher"),

        array("Then I realized that secrecy is actually to the detriment of my own peace of mind and self, and that I could still sustain my belief in privacy and be authentic and transparent at the same time. It was a pretty revelatory moment, and there's been a liberating force that's come from it.", "Alanis Morissette"),

        array("So long as the laws remain such as they are today, employ some discretion: loud opinion forces us to do so; but in privacy and silence let us compensate ourselves for that cruel chastity we are obliged to display in public.", "Marquis de Sade"),

        array("A new father quickly learns that his child invariably comes to the bathroom at precisely the times when he's in there, as if he needed company. The only way for this father to be certain of bathroom privacy is to shave at the gas station.", "Bill Cosby"),

        array("I can't in good conscience allow the U.S. government to destroy privacy, internet freedom and basic liberties for people around the world with this massive surveillance machine they're secretly building.", "Edward Snowden"),

        array("The worst thing about being famous is the invasion of your privacy.", "Justin Timberlake"),

        array("I'm a very private person. I like staying home and doing my stuff. I hate people invading on my privacy. I hate talking about my private life.", "Gisele Bundchen"),

        array("Historically, privacy was almost implicit, because it was hard to find and gather information. But in the digital world, whether it's digital cameras or satellites or just what you click on, we need to have more explicit rules - not just for governments but for private companies.", "Bill Gates"),

        array("Once you've lost your privacy, you realize you've lost an extremely valuable thing.", "Billy Graham"),

        array("When ghetto living seems normal, you have no shame, no privacy.", "Malcolm X"),

        array("The closing of a door can bring blessed privacy and comfort - the opening, terror. Conversely, the closing of a door can be a sad and final thing - the opening a wonderfully joyous moment.", "Andy Rooney"),

        array("I need privacy. I would think that because what I do makes a lot of people happy that I might deserve a little bit of respect in return. Instead, the papers try to drag me off my pedestal.", "Jim Carrey"),

        array("A career is born in public - talent in privacy.", "Marilyn Monroe"),

        array("Privacy is not something that I'm merely entitled to, it's an absolute prerequisite.", "Marlon Brando"),

        array("You use your money to buy privacy because during most of your life you aren't allowed to be normal.", "Johnny Depp"),

        array("Regarding social media, I really don't understand what appears to be the general population's lack of concern over privacy issues in publicizing their entire lives on the Internet for others to see to such an extent... but hey it's them, not me, so whatever.", "Axl Rose"),

        array("I don't want to write an autobiography because I would become public property with no privacy left.", "Stephen Hawking"),

        array("I am absolutely opposed to a national ID card. This is a total contradiction of what a free society is all about. The purpose of government is to protect the secrecy and the privacy of all individuals, not the secrecy of government. We don't need a national ID card.", "Ron Paul"),

        array("When it comes to privacy and accountability, people always demand the former for themselves and the latter for everyone else.", "David Brin"),

        array("I grew up with the understanding that the world I lived in was one where people enjoyed a sort of freedom to communicate with each other in privacy, without it being monitored, without it being measured or analyzed or sort of judged by these shadowy figures or systems, any time they mention anything that travels across public lines.", "Edward Snowden"),

        array("When a young non-white male is stopped and searched at the whim of a police officer, his idea of personal space, privacy and self esteem are shattered, to say nothing of his Fourth and Fourteenth Amendment protections. The damage goes deep quickly and stays. Stop &amp; frisk, as well as a tactic, is also an incitement.", "Henry Rollins"),

        array("Sometimes the heart sees what is invisible to the eye.", "H. Jackson Brown, Jr."),

        array("I give the fight up: let there be an end, a privacy, an obscure nook for me. I want to be forgotten even by God.", "Robert Browning"),

        array("Relying on the government to protect your privacy is like asking a peeping tom to install your window blinds.", "John Perry Barlow"),

        array("With the advent of Twitter and Facebook and other social networking sites, genuine privacy can only be found by renting a private villa for a holiday. Hotels are now out of the question for my wife and I.", "Robert Powell"),

        array("Most of my songs have names of people I've met or are dear to me. There are people who have privacy issues and about people knowing about their private life. But for me, I like to include few special names and few details about them to make the song very special to me.", "Taylor Swift"),

        array("I have as much privacy as a goldfish in a bowl.", "Princess Margaret"),

        array("If we don't act now to safeguard our privacy, we could all become victims of identity theft.", "Bill Nelson"),

        array("I don't see myself as a hero because what I'm doing is self-interested: I don't want to live in a world where there's no privacy and therefore no room for intellectual exploration and creativity.", "Edward Snowden"),

        array("Fame, do I like it? No. It has bought a lot for me in my career, but there are a lot of downsides to it. You give up your privacy. I did it to myself but not to my family and friends. You don't ask for it. You just have to live with it.", "Cara Delevingne"),

        array("I think we live in a world where the most important thing is daily life: sharing a space with your family, making meals, being with your people. It's not only the idea of privacy, it's the beauty of the moment, at a time in the world when everything goes really fast - too fast.", "Ana Tijoux"),

        array("Privacy is not an option, and it shouldn't be the price we accept for just getting on the Internet.", "Gary Kovacs"),

        array("A book is the only place in which you can examine a fragile thought without breaking it, or explore an explosive idea without fear it will go off in your face. It is one of the few havens remaining where a man's mind can get both provocation and privacy.", "Edward P. Morgan"),

        array("I like texting as much as the next kidult - and embrace it as yet more evidence, along with email, that we live now in the post-aural age, when an unsolicited phone call is, thankfully, becoming more and more understood to be an unspeakable social solecism, tantamount to an impertinent invasion of privacy.", "Will Self"),

        array("I feel like everyone has the right to privacy, even if you're the most famous person in the world.", "Marina and the Diamonds"),

        array("I value my privacy and my personal life - and I certainly don't exploit my personal life.", "Scarlett Johansson"),

        array("It's just as difficult to live in a self-made hell of privacy as it is to live in a self-made hell of publicity.", "Michael Hutchence"),

        array("The virtue of privacy is one that must be protected in matters that are intimate and within one's own family.", "Tiger Woods"),

        array("You know, you can make a small mistake in language or etiquette in Britain, or you could when I was younger, and really be made to feel it, and it's the flick of a lash, but it would sting, and especially at school where there's not much privacy, and so on. You could, yes, undoubtedly be made to feel crushed.", "Christopher Hitchens"),

        array("Without whining and without making myself a tragic figure, there is no replacement for the loss of your privacy. It's a huge sacrifice.", "David Duchovny"),

        array("I don't tweet, I don't go on Facebook. I think there's too much information about all of us out there. I'm liking the idea of privacy more and more.", "George Clooney"),

        array("A memoir is an invitation into another person's privacy.", "Isabel Allende"),

        array("I've always retained my privacy, but now I protect it even more.", "Daniel Craig"),

        array("One of the great penalties those of us who live our lives in full view of the public must pay is the loss of that most cherished birthright of man's, privacy.", "Mary Pickford"),

        array("The bigger the network, the harder it is to leave. Many users find it too daunting to start afresh on a new site, so they quietly consent to Facebook's privacy bullying.", "Evgeny Morozov"),

        array("I enjoyed having a reputation as being wild, but these days I try not to worry about what people think in the privacy of their own brain or what they write in the bizarre publicity of their own newspapers, because all of those things are meaningless.", "Russell Brand"),

        array("I believe that the freedom of speech should be protected, but so should a family's right to privacy as they grieve their loss. There is a time and a place for vigorous debate on the War on Terror, but during a family's last goodbye is not it.", "Dave Reichert"),

        array("Despite everything, I believe that people are really good at heart.", "Anne Frank"),

        array("You have to make a lot of sacrifices, and the main thing you have to sacrifice is your privacy. It's funny because when I was growing up, my daddy was and still is an insurance agent in our home town. He couldn't go anywhere without somebody recognizing him or needing something from him.", "Josh Turner"),

        array("Even though now I'm pretty popular in my country and tennis is the No. 1 sport, and I'm very flattered that the people recognise me and come up and give me compliments, I'm more a person who likes to have privacy and peace.", "Novak Djokovic"),

        array("I enjoy privacy. I think it's nice to have a little mystery. I think because of technology a lot of the mystery is gone in life, and I'd like to preserve some of that.", "Maggie Q"),

        array("For me, getting comfortable with being famous was hard - that whole side of it, the loss of anonymity, the loss of privacy. Giving up that part of your life and not having control of it.", "Michelle Pfeiffer"),

        array("I feel like the quality of privacy and respect of people's personal space has been completely disintegrated. You can ask to take the picture. I will be so glad to take the picture and pose and look good for the picture.", "Busta Rhymes"),

        array("I cherish my privacy, and woe betide anyone who tries to interfere with that.", "Jeff Beck"),

        array("These displays of affection mean a lot to our family and are a reminder of the heart that my people have. In this time of grief we ask for a little privacy and space to digest this news; our sister was our sun and we are broken by her departure.", "Amaury Nolasco"),

        array("It's dangerous when people are willing to give up their privacy.", "Noam Chomsky"),

        array("The emphasis must be not on the right to abortion but on the right to privacy and reproductive control.", "Ruth Bader Ginsburg"),

        array("I let people fill in the blanks on their own. If they want to think about their ex, that's fine. If they want to think about maybe who one of my exes is, then that's fine. And it might not be right, because I'm the only one who knows what these songs are really about. It's the one shred of privacy I have in the matter.", "Taylor Swift"),

        array("I believe that any violation of privacy is nothing good.", "Lech Walesa"),

        array("When you fall in love, you wanna share it with people but you know there are some things that you need to keep to yourself, 'cause privacy makes things last longer, I feel.", "Brandy Norwood"),

        array("I do get recognized, but I must say Edinburgh is a fantastic city to live if you're well-known. There is an innate respect for privacy in Edinburgh people, and I also think they're used to seeing me walking around, so I don't think I'm a very big deal.", "J. K. Rowling"),

        array("Privacy and security are those things you give up when you show the world what makes you extraordinary.", "Margaret Cho"),

        array("The institutions that we've built up over the years to protect our individual privacy rights from the government don't apply to the private sector. The Fourth Amendment doesn't apply to corporations. The Freedom of Information Act doesn't apply to Silicon Valley. And you can't impeach Google if it breaks its 'Don't be evil' campaign pledge.", "Al Franken"),

        array("Privacy is not explicitly spelled out in the Constitution as freedom of speech is in the First Amendment.", "Larry Flynt"),

        array("Indeed, an entire generation of Americans has grown to adulthood since the Roe decision of 1973, which held that the right to choose an abortion was a privacy right protected by our Constitution.", "Robert Casey"),

        array("On behalf of NARAL Pro-Choice America - and our one million member activists - I am honored to be here to talk to you about what's at stake for women in 2012. I am proud to say that the Democratic Party believes that women have the right to choose a safe, legal abortion with dignity and privacy.", "Nancy Keenan"),

        array("The incentive for digging up gossip has become so great that people will break the law for the opportunity to take that picture. Then it crosses the line into invasion of privacy. The thing that's really bad about it, though, is that the tabloids don't tell the truth.", "Vince Vaughn"),

        array("Our privacy is starting to be invaded and we can't get anything done. I'm happy with the fundraising but upset we don't have time to talk and meet with people.", "Terry Fox"),

        array("Publication is a self-invasion of privacy.", "Marshall McLuhan"),

        array("The words that a father speaks to his children in the privacy of home are not heard by the world, but, as in whispering galleries, they are clearly heard at the end, and by posterity.", "Jean Paul"),

        array("Truth should not be forced; it should simply manifest itself, like a woman who has in her privacy reflected and coolly decided to bestow herself upon a certain man.", "John Updike"),

        array("There are only two occasions when Americans respect privacy, especially in Presidents. Those are prayer and fishing.", "Herbert Hoover"),

        array("I like the privacy of my life and I protect it quite vigilantly.", "Nicole Kidman"),

        array("The only true source of politeness is consideration.", "William Gilmore Simms"),

        array("You have to fight for your privacy or you lose it.", "Eric Schmidt"),

        array("I'm not a kid. You don't get in this business for anonymity. It's not like I have posters of myself on the wall, but at the same time, I'm kind of ready for a little bit of it, but I worry for my little one, and my family - their privacy. That's what I'm more protective of.", "Hugh Jackman"),

        array("I think we're seeing privacy diminish, not by laws... but by young people who don't seem to value their privacy.", "Alan Dershowitz"),

        array("According to the Privacy Rights Center, up to 10 million Americans are victims of ID theft each year. They have a right to be notified when their most sensitive health data is stolen.", "Luis Gutierrez"),

        array("Privacy is something I have come to respect. I think when I was younger I wanted to tell everybody everything, because I thought I was so damn interesting. Then I heard the snoring.", "David Duchovny"),

        array("There is nothing new in the realization that the Constitution sometimes insulates the criminality of a few in order to protect the privacy of us all.", "Antonin Scalia"),

        array("Facebook says, 'Privacy is theft,' because they're selling your lack of privacy to the advertisers who might show up one day.", "Jaron Lanier"),

        array("I want my son - and my kids, if I have more - to grow up in a way that is as anonymous as possible. The fact that his father and I have chosen to do the work that we do doesn't give anybody the right to invade our privacy.", "Penelope Cruz"),

        array("It's the interviewee's job to know that his privacy is going to be invaded on some level. Otherwise, you are better off not doing the interview.", "John Travolta"),

        array("Law-abiding citizens value privacy. Terrorists require invisibility. The two are not the same, and they should not be confused.", "Richard Perle"),

        array("I respect someone's right to privacy and I want them to know it.", "Terry Gross"),

        array("You know, we're very private, and I think that we really separate and try to keep our privacy to ourselves. There's things that people assume a lot of times, and we understand that people are interested, but we really try to keep our family life private as much as we can.", "Tim McGraw"),

        array("Every American deserves to live in freedom, to have his or her privacy respected and a chance to go as far as their ability and effort will take them - regardless of race, gender, ethnicity or economic circumstances.", "Christopher Dodd"),

        array("Ultimately, the reason privacy is so vital is it's the realm in which we can do all the things that are valuable as human beings. It's the place that uniquely enables us to explore limits, to test boundaries, to engage in novel and creative ways of thinking and being.", "Glenn Greenwald"),

        array("I have to understand what my strengths and limitations are, and work from a true place. I try to do this as best I can while still protecting my writer self, which more than ever needs privacy.", "Sandra Cisneros"),

        array("If the right to privacy means anything, it is the right of the individual, married or single, to be free from unwarranted governmental intrusion.", "William J. Brennan, Jr."),

        array("Privacy is one of the biggest problems in this new electronic age.", "Andy Grove"),

        array("My privacy is very intentional.", "Cheryl Cole"),

        array("Privacy with medical information is a fallacy. If everyone's information is out there, it's part of the collective.", "Craig Venter"),

        array("Privacy under what circumstance? Privacy at home under what circumstances? You have more privacy if everyone's illiterate, but you wouldn't really call that privacy. That's ignorance.", "Bruce Sterling"),

        array("Nothing that we have authorized conflicts with any law regarding privacy or any provision of the constitution.", "John Ashcroft"),

        array("I believe in my privacy. I always have, and I always will. I don't think that my private life needs to be on display for me to get a better response at the box office or for me to get a better choice of movies.", "Kajol"),

        array("My biggest thing has always been privacy. With an interview such as this where the questions are about me, I struggle to express myself. I have an immediate answer in my head of what I'd say, but sometimes I feel that it would be too honest. So these wheels of censorship start going around my head.", "Garrett Hedlund"),

        array("I drive myself to and from work. I love the privacy.", "Bob Iger"),

        array("I do suspect that privacy was a passing fad.", "Larry Niven"),

        array("I pretended to be somebody I wanted to be until finally I became that person. Or he became me.", "Cary Grant"),

        array("For me, privacy and security are really important. We think about it in terms of both: You can't have privacy without security.", "Larry Page"),

        array("Your employer is the last person you should want to provide for your healthcare, from a privacy, financial, and value standpoint. Employees with families should get the family, meaning spouses and children, off the company plan. In most cases, that will save them money.", "Paul Zane Pilzer"),

        array("I am not sure precisely why we need to have privacy, but everyone knows for sure that we need to relax and not have to put on our social, outwardly looking face all of the time.", "Ezekiel Emanuel"),

        array("Taking privacy cues from the federal government is - to say the least - ironic, considering today's Orwellian level of surveillance. At virtually any given time outside of one's own home, an American citizen can reasonably assume his movements and actions are being monitored by something, by somebody, somewhere.", "Bob Barr"),

        array("Being a Brady comes with it's pleasures and its baggage. I'm not one given to a lack of privacy and invasion.", "Christopher Knight"),

        array("Taxpayers should not be coerced into giving up their privacy rights just to file their taxes.", "Melissa Bean"),

        array("Everything is accessible to everyone all the time, and I think there are wondrous things to treasure with what the Internet has made available to journalists. But I think it's also had some effects that are less pleasant. It has chipped away at a sense of privacy and secrecy.", "Bill Keller"),

        array("We demand privacy, yet we glorify those that break into computers.", "Bill McCollum"),

        array("I realize at one point, that I was being followed, and then I began to see the surveillance that was going past the road on my house. And so, these cars began to surveil me. People began to follow me around, and it did, it was very disrupting to think that your privacy was being violated, and for no reason that I could come up with.", "Gloria Naylor"),

        array("The American people must be willing to give up a degree of personal privacy in exchange for safety and security.", "Louis Freeh"),

        array("I am a private person; I think that's important if you're an actor. But there's a difference between privacy and secrecy, and I'm not a secretive person.", "Andrew Scott"),

        array("Our world is utterly saturated with fear. We fear being attacked by religious extremists, both foreign and domestic. We fear the loss of political rights, a loss of privacy, or a loss of freedom. We fear being injured, robbed or attacked, being judged by others, or neglected, or left unloved.", "Brendan Myers"),

        array("Far from being the basis of the good society, the family, with its narrow privacy and tawdry secrets, is the source of all our discontents.", "Edmund Leach"),

        array("That was one of the most comfortable things about leaving baseball was to leave the environment. It's very much like a rock star existence - the nightlife, the hotels, lack of privacy... There's a lot of temptations out there. It was nice getting away from it.", "Mike Schmidt"),

        array("If people want to invade your privacy, they want to invade your privacy. I find it chilling, and I find it awful, and it makes me really nervous. It hasn't happened to me much, but when you have a taste of it, it's bitter.", "Ruth Negga"),

        array("We must restrict the anonymity behind which people hide to commit crimes. As citizens, we have a right to privacy. We have no such right to anonymity.", "Edgar Bronfman, Jr."),

        array("We want to be sensitive to people's concerns about privacy about their personal being and things, while ensuring that everybody on every flight has been properly screened.", "John Pistole"),

        array("I just love my privacy.", "Ann-Margret"),

        array("Diana and I had a very good relationship with no personal problems. The only problem we did have was with the media, and the only place we could have any real privacy was at Kensington Palace, as they could not get to us there.", "Hasnat Khan"),

        array("I certainly respect privacy and privacy rights. But on the other hand, the first function of government is to guarantee the security of all the people.", "Phil Crane"),

        array("This has been a learning experience for me. I also thought that privacy was something we were granted in the Constitution. I have learned from this when in fact the word privacy does not appear in the Constitution.", "Bill Maher"),

        array("I'm worried about privacy because of the young people who don't give a damn about their privacy, who are prepared to put their entire private lives online. They put stuff on Facebook that 15 years from now will prevent them from getting the jobs they want. They don't understand that they are mortgaging their future for a quick laugh from a friend.", "Alan Dershowitz"),

        array("Sometimes, giving up your privacy is a little like going to the dentist and we have let him have access that no one's ever had.", "Tom Petty"),

        array("Any privacy in public is a hard thing to negotiate.", "Benedict Cumberbatch"),

        array("I'm one of those people who fiercely guards their privacy, so I hate doing interviews.", "Megan Fox"),

        array("There is no pleasure in having nothing to do; the fun is having lots to do and not doing it.", "Andrew Jackson"),

        array("When people talked about protecting their privacy when I was growing up, they were talking about protecting it from the government. They talked about unreasonable searches and seizures, about keeping the government out of their bedrooms.", "Al Franken"),

        array("Let those who know know, and let me keep what little privacy I can.", "Lisa Bonet"),

        array("The U.S. Constitution protects our privacy from the prying eyes of government. It does not, however, protect us from the prying eyes of companies and corporations.", "Simon Sinek"),

        array("The people who are worried about privacy have a legitimate worry. But we live in a complex world where you're going to have to have a level of security greater than you did back in the olden days, if you will. And our laws and our interpretation of the Constitution, I think, have to change.", "Michael Bloomberg"),

        array("I'm learning to accept the lack of privacy as the real downer in my profession.", "Halle Berry"),

        array("I want my government to do something about my privacy - I don't want to just do it on my own.", "Evgeny Morozov"),

        array("You already have zero privacy - get over it.", "Scott McNealy"),

        array("People aren't interested in others controlling what they can do or read or see in the privacy of their own homes.", "Larry Flynt"),

        array("Privacy about giving is counterproductive. There is solid scientific research showing that people are more likely to give if they can see that others are giving. The richest people, in particular, should be setting an example.", "Peter Singer"),

        array("How many of you have broken no laws this month? That's the kind of society I want to build. I want a guarantee - with physics and mathematics, not with laws - that we can give ourselves real privacy of personal communications.", "John Gilmore"),

        array("I know I can't dance. I am the worst dancer. I have no rhythm. I just do step-and-snap. I love it in the privacy of my own home and every once in a while at a club. But singing and dancing are my two greatest fears.", "Hope Solo"),

        array("I hate that tabloid idea of anybody who is famous having to forfeit their privacy.", "Caitlin Moran"),

        array("I was suited for fame, and I mean that in the most non-egocentric way. I don't mind gearing my life towards privacy. It's my nature.", "John Travolta"),

        array("I think Democrats are right. We fight for the American dream, for the environment, for privacy rights, a woman's right to choose, a good public education system.", "Barbara Boxer"),

        array("Human beings are not meant to lose their anonymity and privacy.", "Sarah Chalke"),

        array("There is a massive apparatus within the United States government that with complete secrecy has been building this enormous structure that has only one goal, and that is to destroy privacy and anonymity, not just in the United States, but around the world.", "Glenn Greenwald"),

        array("Poverty is relative, and the lack of food and of the necessities of life is not necessarily a hardship. Spiritual and social ostracism, the invasion of your privacy, are what constitute the pain of poverty.", "Alice Foote MacDougall"),

        array("I do mind some of the intrusions on privacy.", "Mary Archer"),

        array("I'm not on Facebook. I'm not on Twitter. I know a lot of celebrities who go around complaining how little privacy they have.", "Cote de Pablo"),

        array("I believe in a zone of privacy.", "Hillary Clinton"),

        array("Unless a president can protect the privacy of the advice he gets, he cannot get the advice he needs.", "Richard M. Nixon"),

        array("Fortunately, we have help from the media. I have to say this: I'm very grateful for the support and kindness that we've gotten. People have respected their privacy and in that way, I think, you know, no matter what people may feel about my husband's policies or what have you, they care about children and that's been good to see.", "Michelle Obama"),

        array("The society of dead authors has this advantage over that of the living: they never flatter us to our faces, nor slander us behind our backs, nor intrude upon our privacy, nor quit their shelves until we take them down.", "Charles Caleb Colton"),

        array("I'm not that ambitious any more. I just like my privacy. I wish I really wasn't talked about at all.", "Barbra Streisand"),

        array("It's a big challenge for me to keep my integrity and some of my privacy intact.", "Sarah McLachlan"),

        array("A creative man is motivated by the desire to achieve, not by the desire to beat others.", "Ayn Rand"),

        array("I just knew at an early time in my life how important privacy was.", "Daniel Day-Lewis"),

        array("I have been called a nun with a switchblade where my privacy is concerned. I think there's a point where one says, that's for family, that's for me.", "Julie Andrews"),

        array("I don't always want my opinion known. What little privacy I have left I'd like to maintain.", "Calvin Klein"),

        array("What a terrible thing it would be to be the Pope! What unthinkable responsibilities to fall on your shoulders at an advanced age! No privacy. No seclusion. No sin.", "Roger Ebert"),

        array("The issue is privacy. Why is the decision by a woman to sleep with a man she has just met in a bar a private one, and the decision to sleep with the same man for $100 subject to criminal penalties?", "Anna Quindlen"),

        array("I suspect privacy is a very new concept to humanity.", "Helen Fisher"),

        array("Google's screen for privacy settings does give you more options for what you share than Apple's does. But it's not a complete list, and people aren't aware of whether or not that information will go to a third party.", "Al Franken"),

        array("It's hard to just kinda get some privacy and do your own thing.", "Shaun White"),

        array("If people are constantly reading about you, and you're overexposed, they've got no reason to go see your movies. Also, it's not pleasant or nice to have your privacy invaded.", "John Cusack"),

        array("Really, life is complicated enough without having a bunch of Senators deciding what we should do in the privacy of our own homes.", "Barbara Boxer"),

        array("I've learned the hard way how valuable privacy is. And I've learned that there are a lot of things in your life that really benefit from being private. And relationships are one of them.", "Ashton Kutcher"),

        array("I am of mixed minds about the issue of privacy. On one hand, I understand that information is power, and power is, well, power, so keeping your private information to yourself is essential - especially if you are a controversial figure, a celebrity, or a dissident.", "Susan Orlean"),

        array("From the moment I walked into the White House, it was as if I had no privacy at all.", "Nancy Reagan"),

        array("All the legal action I've taken against newspapers has had a massively positive effect on my life and achieved exactly what I wanted, which is privacy and non-harassment.", "Sienna Miller"),

        array("At one point I thought changing my name might help with privacy, but that was before the Internet.", "Olivia Wilde"),

        array("In a sense, I'm always hearing music of some sort, whether it's people talking or surface noise or whatever, because there is no privacy. So when I'm by myself, I just kind of like to be and reflect, and I can't do that when I'm listening to music. Because it's someone else's reflections, not mine.", "Sarah McLachlan"),

        array("I think privacy is important, and it's important you don't bore people with your own boring self.", "Alison Jackson"),

        array("Although I am a public figure, I'm still a little shy. I don't think my own personality is important. I prefer to keep some small dosage of privacy.", "Joshua Lederberg"),

        array("We need to start seeing privacy as a commons - as some kind of a public good that can get depleted as too many people treat it carelessly or abandon it too eagerly. What is privacy for? This question needs an urgent answer.", "Evgeny Morozov"),

        array("Personal privacy is a closely held American value.", "Anna Eshoo"),

        array("Oh, well, there's a difference between privacy and secrecy.", "Laura Schlessinger"),

        array("Issues such as transparency often boil down to which side of - pick a number - 40 you're on. Under 40, and transparency is generally considered a good thing for society. Over 40, and one generally chooses privacy over transparency. On every side of this issue, hypocrisy abounds.", "Graydon Carter"),

        array("I don't mind gearing my life towards privacy. It's my nature.", "John Travolta"),

        array("We've come to expect so little from online privacy measures that public displays of concern about the matter are more or less for show. Being devastated to discover you've been tagged in somebody else's photo has an air of the melodramatic about it at this point.", "Sloane Crosley"),

        array("I might have lived in England for the last several years, but I'm still an American citizen and I have not given up my right to privacy.", "Kevin Spacey"),

        array("Excellence is doing ordinary things extraordinarily well.", "John W. Gardner"),

        array("I never like other people to clean for me. I don't want them to invade my own privacy.", "Bess Myerson"),

        array("The companies that do the best job on managing a user's privacy will be the companies that ultimately are the most successful.", "Fred Wilson"),

        array("We need to codify our values and build consensus around what we want from a free society and a free Internet. We need to put into law protections for our privacy and our right to speak and assemble.", "Heather Brooke"),

        array("As a culture I see us as presently deprived of subtleties. The music is loud, the anger is elevated, sex seems lacking in sweetness and privacy.", "Shelley Berman"),

        array("Isn't privacy about keeping taboos in their place?", "Kate Millett"),

        array("The Obama administration says we only destroy the privacy of non-Americans. That is not true. The government is spying on Americans.", "Glenn Greenwald"),

        array("I can't understand why anyone would want to live the life of a politician if you can't say pretty much what you think. You are not in it for the money: there's unremitting pressure on your life, you give up so much of your privacy. It can only be because of the things you want to do and the things you want to say.", "Ken Livingstone"),

        array("I did not become successful in my work through embracing or engaging in celebrity culture. I never signed away my privacy in exchange for success.", "Steve Coogan"),

        array("I don't think I responded very well to the sudden celebrity, the sudden fame, and the loss of privacy.", "David Schwimmer"),

        array("I have a very good sense of tone, and it's possible to talk about very personal things and maintain a level of dignity and even privacy - to go to the place, to talk about it, but not get icky.", "Jane Pauley"),

        array("But the time has come for journalists to acknowledge that a zone of privacy does exist.", "Roger Mudd"),

        array("The right to privacy has both positive and negative connotations for those who consider themselves part of the natural law tradition.", "David Novak"),

        array("As a social good, I think privacy is greatly overrated because privacy basically means concealment. People conceal things in order to fool other people about them. They want to appear healthier than they are, smarter, more honest and so forth.", "Richard Posner"),

        array("It's the off-the-court spotlight in terms of having people look at you in terms of analyzing every little thing you do in your life, or having less privacy in your day-to-day activities, that's an area I need to get more accustomed to.", "Jeremy Lin"),

        array("No one can train you to be famous. How do you deal with the loss of anonymity, the loss of privacy? You have to be disciplined.", "Wesley Snipes"),

        array("I've been careful to keep my life separate because it's important to me to have privacy and for my life not to be a marketing device for a movie or a TV show. I'm worth more than that.", "Lisa Kudrow"),

        array("Even rock stars are entitled to privacy.", "Michael Novak"),

        array("The audience plays a huge part in how a piece will actually form. They really allow the performers to walk a tightrope in a way that never seems to happen in the privacy of your own four walls. I'm listening to the audience, and they're listening to me.", "Evelyn Glennie"),

        array("Privacy is big for me. To do interviews even, I have a very love/hate with it.", "Zach Galifianakis"),

        array("I really believe that we don't have to make a trade-off between security and privacy. I think technology gives us the ability to have both.", "John Poindexter"),

        array("I think there is a possible future where maybe we do just take a hard turn away from the Internet and we do start valuing our privacy again.", "Brian K. Vaughan"),

        array("Do whatever you want to do in the privacy of your own home.", "Willie Geist"),

        array("An autobiography is not about pictures; it's about the stories; it's about honesty and as much truth as you can tell without coming too close to other people's privacy.", "Boris Becker"),

        array("I'm fiercely protective of my privacy.", "Carla Gugino"),

        array("I have no privacy anymore.", "Felix Baumgartner"),

        array("Nothing can be done except little by little.", "Charles Baudelaire"),

        array("I am not a fan of Facebook or Twitter. They both allow too much information to be available and they make privacy a thing of the past.", "Kirsty Gallacher"),

        array("It's a weird thing because I've been single at the time when I've been successful. That's good and bad. Good, because you meet lots of people, bad because your privacy is infringed, so it's harder to develop things.", "David Walliams"),

        array("Media reporting denied privacy to anybody doing what I do for a living. It was no longer possible to work on your picture in privacy.", "James L. Brooks"),

        array("People have less privacy and are crammed together in cities, but in the wide open spaces they secretly keep tabs on each other a lot more.", "Sara Paretsky"),

        array("I wish that when we weren't filming, we could have full privacy. I wish I could live in a bubble and just be with my family.", "Kourtney Kardashian"),

        array("First, the security and privacy of sensitive taxpayer information is absolutely essential.", "Jim Ramstad"),

        array("I never Tweet about my daughter. Never. I just want to be respectful of her privacy. My job as a mom is to know when to open my mouth and when not to.", "Padma Lakshmi"),

        array("I really fight for my privacy.", "Kyle MacLachlan"),

        array("Where it gets clear for me about the privacy issue is with my kids because they didn't choose this kind of life. I'm an incredibly open person, though - I'll tell anyone anything.", "Carrie-Anne Moss"),

        array("With existing technology, we can enforce airport security without sacrificing our personal privacy.", "Tom Udall"),

        array("I am a technological activist. I have a political agenda. I am in favor of basic human rights: to free speech, to use any information and technology, to purchase and use recreational drugs, to enjoy and purchase so-called 'vices', to be free of intruders, and to privacy.", "Bram Cohen"),

        array("Every ISP is being attacked, maliciously both from in the United States and outside of the United States, by those who want to invade people's privacy. But more importantly they want to take control of computers, they want to hack them, they want to steal information.", "Darrell Issa"),

        array("The judgment means a lot. As a journalist being accused of invading someone's privacy, there is always a risk that it will stick to your name.", "Asne Seierstad"),

        array("I don't think when people sign up for a life of doing something they love to do they should have to sign up for a complete loss of privacy. I understand a little loss of privacy coming with the job.", "Sarah Chalke"),

        array("As a result of this article, I was invited to testify in the Senate Judiciary Committee on privacy law.", "Norman Lamm"),

        array("Being out in the street is not an expectation of privacy. Anyone can look at you, can see you, can watch what you're doing.", "Peter T. King"),

        array("In the early 1980s, I wrote a book called 'The Complete Guide to Financial Privacy.' If I would write that book today, it would be a pamphlet. There is precious little privacy left.", "Mark Skousen"),

        array("If I was going to make a broad generalisation, I'd say that I prefer the company of women. People know now that I live with Mike Figgis, but I prefer not to talk about it. On one level, privacy is important, but on another level I have no desire to deny certain things.", "Saffron Burrows"),

        array("You become a celebrity, not because of your work or what you do, but because you have no privacy.", "Lisa Kudrow"),

        array("I had to focus on some personal areas in my life with the little bit of privacy that I have.", "Mario Vazquez"),

        array("I mean, I don't want to sound - of course it's very nice, people come up and say appreciative things about my work. But the loss, in terms of privacy and anonymity, is no small thing to me.", "Todd Solondz"),

        array("I'm worried about privacy - the companies out there gathering data on us, the stuff we do on Twitter, the publicly scrapeable stuff on Facebook. It's amazing how much data there is out there on us. I'm worried that it can be abused and will be abused.", "Michael Arrington"),

        array("Anyone who lives in Washington and has an official position viscerally understands the cost of a lack of privacy. Every dinner - especially ones with a journalist in attendance - is preceded by the mandatory, 'This is off the record.' But everyone also knows, nothing is really 'off the record.'", "Ezekiel Emanuel"),

        array("I do think, even though you are a public figure, I do think you should be entitled to your privacy, and I do think that there are things that go on in relationships and behind closed doors that are completely private.", "Tamara Ecclestone"),

        array("The Microsoft actions announced today are exactly the kinds of industry initiatives we need. Microsoft is using its resources to bring real privacy protection to Internet users by creating incentives for more websites to provide strong privacy protection.", "William M. Daley"),

        array("Flying is learning how to throw yourself at the ground and miss.", "Douglas Adams"),

        array("Realize that a Muslim will know that his wife was seen naked in this machine. You know what would be the reaction?... Terrible. I believe there's technology out there that can identify bomb-type materials without necessarily, overly invading our privacy.", "Isaac Yeffet"),

        array("Privacy was in sufficient danger before TV appeared, and TV has given it its death blow.", "Louis Kronenberger"),

        array("We must carefully consider card security solutions, such as adding photographs or machine-readable electronic strips, so to prevent further breaches of individual privacy that could result from changes to the design of Social Security Cards.", "Ron Lewis"),

        array("Being very famous is not the fun it sounds. It merely means you're being chased by a lot of people and you lose your privacy.", "Colin Wilson"),

        array("Judaism is much more communal, and partly as a consequence of my religious switch, I am increasingly more suspicous of my previous view that what people do in the privacy of their own home is their business alone.", "Luke Ford"),

        array("At the end of the whole day of working with people you want some privacy.", "Bill Bruford"),

        array("You lose your privacy, and sometimes, people don't see you as human.", "Shawn Wayans"),

        array("I don't mind talking about my two daughters, but I don't feel comfortable denying them their privacy.", "Tom Bergeron"),

        array("If you look at Griswold, what you can see is the first time the Court recognized the right to privacy, which ends up becoming ultimately the right to abortion.", "Jay Alan Sekulow"),

        array("If privacy ends where hypocrisy begins, Kitty Kelley's steamy expose is a contribution to contemporary history.", "Eleanor Clift"),

        array("Whether it's Facebook or Google or the other companies, that basic principle that users should be able to see and control information about them that they themselves have revealed to the companies is not baked into how the companies work. But it's bigger than privacy. Privacy is about what you're willing to reveal about yourself.", "Eli Pariser"),

        array("Foreigners like me have no privacy rights whatsoever. Yet we keep using U.S.-based services all the time, making us a legal target for gathering and storing our private information. Other countries do surveillance as well. But nobody has the global visibility that United States does.", "Mikko Hypponen"),

        array("I'm way bigger than people think I am. I'm way bigger. I've been underrated all my life, and that's fine. I have privacy. I can walk the street without being hassled. I can be a regular guy. The price to give that up is so horrible. When you become a part of the hysteria - it's not completely in my hands - you have to hide.", "Rutger Hauer"),

        array("I like to have my privacy. I don't like people knowing what I do in my free time. I am also a very shy person, but I understand that people want to know more.", "Ana Ivanovic"),

        array("Writing can sometimes be exploitative. I like to take a few steps of remove in order to respect the privacy of the subject. If readers make the link, they have engaged with the poem.", "John Barton"),

        array("In exchange for power, influence, command and a place in history, a president gives up the bulk of his privacy.", "Roger Mudd"),

        array("Most journalists now believe that a person's privacy zone gets smaller and smaller as the person becomes more and more powerful.", "Roger Mudd"),

        array("I have written a memoir here and there, and that takes its own form of selfishness and courage. However, generally speaking, I have no interest in writing about my own life or intruding in the privacy of those around me.", "Peter Carey"),

        array("The Oscar changed everything. Better salary, working with better people, better projects, more exposure, less privacy.", "Kathy Bates"),

        array("To put someone in jail for using drugs in the privacy of his hotel room is just barbaric.", "Danny Sugerman"),

        array("We need to and must protect privacy. But I think that people will be willing and even eager to share medical information about themselves for the greater good of mankind.", "Patrick Soon-Shiong"),

        array("I showed that privacy was an implicit right in Jewish law, probably going back to the second or third century, when it was elaborated on in a legal way.", "Norman Lamm"),

        array("People should be allowed to document evidence of criminal wrongdoing. Where is the expectation of privacy if someone is conspiring to commit crime?", "Linda Tripp"),

        array("Where is the expectation of privacy in the commission of a crime?", "Linda Tripp"),

        array("At the bottom, the elimination of spyware and the preservation of privacy for the consumer are critical goals if the Internet is to remain safe and reliable and credible.", "Cliff Stearns"),

        array("The farther reason looks the greater is the haze in which it loses itself.", "Johann Georg Hamann"),

        array("Sharing with just your friends doesn't protect your privacy. I know the people at Facebook will disagree and argue that users can control what is shared with whom. But this is simply an illusion that makes us feel better about all the sharing we have done and are about to do.", "Ben Parr"),

        array("It can feel like an invasion of privacy, involving an employer in a personal matter.", "Frank Murphy"),

        array("When a show becomes a mega hit internationally, you lose a lot of privacy, you become a hider. It's not a human condition we are exposed to very often.", "Steve Kanaly"),

        array("I always felt that a governor surrenders a certain amount of privacy. And I came to accept that.", "James Douglas"),

        array("Facebook is by far the largest of these social networking sites, and starting with its ill-fated Beacon service, privacy concerns have more than once been raised about how the ubiquitous social networking site handles its user data.", "Michael Bennet"),

        array("I think the most privacy I had was when the game was going on.", "Roger Maris"),

        array("Since we enacted the PATRIOT Act almost three years ago, there has been tremendous public debate about its breadth and implications on due process and privacy.", "Howard Berman"),

        array("I like my privacy, and my personal bank manager is one of my favourite people.", "Amanda Eliasch"),

        array("The reason for privacy is not so that people will not know you go to the bathroom. It's to allow certain things to go on that you don't want other people to know about, when all is said and done. But the things I don't want other people to know about are not my sex life.", "Samuel R. Delany"),

        array("It was said of me recently that I suffered from an Obsessional Privacy. I can only suppose it must be true.", "Dirk Bogarde"),

        array("The reason why I've been keeping private for the longest time ever here, I've always wanted to protect my wife's privacy. I don't like - I didn't want to put her picture all over the news. I just wanted to keep her private.", "Michael Schiavo"),

        array("Privacy is a vast subject. Also, remember that privacy and convenience is always a trade-off. When you open a bank account and want to borrow some money, and you want to get a very cheap loan, you'll share all details of your assets because you want them to give you a low interest rate.", "Nandan Nilekani"),

        array("People watch me, waiting for me to slip up, so my privacy has gone - but that's a price you pay.", "Samantha Mumba"),

        array("All violations of essential privacy are brutalizing.", "Katharine Elizabeth Fullerton Gerould"),

        array("I particularly recognize that reasonable people can disagree as to what that proper balance or blend is between privacy and security and safety.", "John Pistole"),

        array("For thirty years, beginning with the invention of a privacy right in the Supreme Court decision Roe v. Wade, the Left has been waging a systematic assault on the constitutional foundation of the nation.", "David Horowitz"),

        array("TIA was being used by real users, working on real data - foreign data. Data where privacy is not an issue.", "John Poindexter"),

        array("America is a noisy culture, unlike, say, Finland, which values silence. Individualism, dominant in the U.S. and Germany, promotes the direct, fast-paced style of communication associated with extraversion. Collectivistic societies, such as those in East Asia, value privacy and restraint, qualities more characteristic of introverts.", "Laurie Helgoe"),

        array("To wait for hours to buy a train ticket or to see a doctor is accepted as a normal way of doing things. Privacy is not a great preoccupation, and this is a very crowded country.", "Nancy Travis"),

        array("We have never really had absolute privacy with our records or our electronic communications - government agencies have always been able to gain access with appropriate court orders.", "Dorothy Denning"),

        array("With those people, I'm very far apart, because I believe that government access to communications and stored records is valuable when done under tightly controlled conditions which protect legitimate privacy interests.", "Dorothy Denning"),

        array("The right of an individual to conduct intimate relationships in the intimacy of his or her own home seems to me to be the heart of the Constitution's protection of privacy.", "Harry A. Blackmun"),

        array("I'm not a natural employer. I live very privately, and we like our privacy at home. To be sitting and talking with your wife or your family and to have somebody walking around and you're ignoring them, I couldn't handle that at all. I can barely handle a cleaning lady coming in every so often.", "Jim Carter"),

        array("But what I want to assure and reassure the public is we are concerned about your safety, your security, and your privacy. Let's work together in partnership to ensure that we can have the best way forward.", "John Pistole"),

        array("I knew from the beginning that privacy was going to be a huge issue, especially with regard to applying Total Information Awareness in counterterrorism. Because if the technology development was successful, a logical place to apply it was inside the United States.", "John Poindexter"),

        array("Three things cannot be long hidden: the sun, the moon, and the truth.", "Buddha"),

        array("I don't like showing my privacy online.", "Eduardo Saverin"),

        array("I was a boarding school product from the age of eight, and I hated it. Though I do have a theory that boarding school is good training for writers because it's so desperately lacking in privacy: you make space for yourself by having an interior life.", "Simon Mawer"),

        array("Whether it's threats to Medicare, cuts in education spending, or Internet privacy, the ramifications got young people out to vote and should be enough to keep them involved in our political system.", "Patrick Murphy"),

        array("We followed the law, we follow our policies, we self-report, we identify problems, we fix them. And I think we do a great job, and we do, I think, more to protect people's civil liberties and privacy than they'll ever know.", "Keith B. Alexander")

);

    public static function next() {
        return self::$_quotes[array_rand(self::$_quotes)];
    }
}
