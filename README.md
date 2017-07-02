# Refactoring Kata Test

## Introduction

**Evaneos** is present on a lot of countries and we have some message templates we want to send
in different languages. To do that, we've developed `TemplateManager` whose job is to replace
placeholders in texts by travel related information.

`TemplateManager` is a class that's been around for years and nobody really knows who coded
it or how it really works. Nonetheless, as the business changes frequently, this class has
already been modified many times, making it harder to understand at each step.

Today, once again, the PO wants to add some new stuff to it and add the management for a new
placeholder. But this class is already complex enough and just adding a new behaviour to it
won't work this time.

Your mission, should you decide to accept it, is to **refactor `TemplateManager` to make it
understandable by the next developer** and easy to change afterwards. Now is the time for you to
show your exceptional skills and make this implementation better, extensible, and ready for future
features.

Sadly for you, the public method `TemplateManager::getTemplateComputed` is called everywhere, 
and **you can't change its signature**. But that's the only one you can't modify (unless explicitly
forbidden in a code comment), **every other class is ready for your changes**.

This exercise **should not last longer than 1 hour** (but this can be too short to do it all and
you might take longer if you want).

You can run the example file to see the method in action.

## Rules
There are some rules to follow:
 - You must commit regularly
 - You must not modify code when comments explicitly forbid it

## Deliverables
What do we expect from you:
 - the link of the git repository
 - several commits, with an explicit message each time
 - a file / message / email explaining your process and principles you've followed

**Good luck!**

1. Cool there is unit tests let's read it to see how we use this `TemplateManager`
2. Okay I install *phpunit* and run these tests -> tests passed that's cool. (I was expecting `DataProviders` in the test to cover a lot of different cases but that will do the job)
3. Let's read this `TemplateManager` and see what basic operation it must do
4. Okay it must found [entity:value] tokens to replace them. So a good way wood be to split these two operations 
5. I have some parts I must not modify, let's see them and see how the code is arranged.
6. Oh a `Singleton` Trait, I love it :)
7. Okay basicly all the Repository parts are forbiden but i don't feel the need to modifiy other classes than `TemplateManager` for now.
8. Let's go "On fout les mais dans le camboui"
9. `getTemplateComputed` seems quite understandable, let's focus on compute Text.
10. My approach would be first get all couples [entity:field] that need to be modified and then replace them. So let's do these functions
11. Let's hardcode for now and i'll try to process using *Regex* later if i have time
12. I don't like `contains***` variables so let's extract a function and let's replace unreadable tests
13. fuck I commit instead of lanching my phpunit ammend is my friend
14. wtf with `$destinationOfQuote` and `$destination` vars and $usefulObject. let's clean this mess
15. I forgot to touch the user part, let's go
16. There is clearly 2 part in `replacetokens` *Quote* and *User.* let's split in two function
17. Time is up :( to go further i will use regex to find [entity:value] in `getAffectedEntitiesAndData` and normalize attribute replace in other classes that will have the logic to render html or pure text or url when needed.