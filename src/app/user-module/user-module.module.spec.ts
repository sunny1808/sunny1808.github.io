import { UserModuleModule } from './user-module.module';

describe('UserModuleModule', () => {
  let userModuleModule: UserModuleModule;

  beforeEach(() => {
    userModuleModule = new UserModuleModule();
  });

  it('should create an instance', () => {
    expect(userModuleModule).toBeTruthy();
  });
});
